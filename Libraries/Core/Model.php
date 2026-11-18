<?php

class Model extends Conexion
{
    protected string $table = "";
    protected string $primaryKey = "id";
    protected string $select = "*";
    protected array $joins = [];
    protected array $whereBuilder = [];
    protected string $orderBy = "";
    protected string $limit = "";

    /** Columnas por defecto del Query Builder (un modelo con JOIN puede redefinirlo). */
    protected string $defaultSelect = "*";

    /** Parámetros (valores externos) asociados a las condiciones WHERE. */
    protected array $bindings = [];

    /** Operadores permitidos en where(["columna operador" => valor]). */
    private const OPERATORS = ["=", "<>", "!=", "<", "<=", ">", ">=", "LIKE"];

    public function __construct()
    {
        parent::__construct();
        $this->resetQuery();
    }

    // ------------------------------------------------------------------
    // CRUD genérico
    // ------------------------------------------------------------------

    public function all(): array
    {
        $sql = "SELECT * FROM {$this->table}";
        return $this->connect()->query($sql)->fetchAll();
    }

    public function find(int $id): array|false
    {
        // Usa el SELECT por defecto y los JOIN permanentes del modelo.
        $row = $this->where(["{$this->table}.{$this->primaryKey}" => $id])->first();
        return $row ?? false;
    }

    public function insert(array $data): string|false
    {
        if (empty($data)) {
            return false;
        }

        $columns = array_keys($data);
        foreach ($columns as $column) {
            $this->assertIdentifier($column);
        }

        $placeholders = implode(", ", array_map(fn($c) => ":{$c}", $columns));
        $sql = "INSERT INTO {$this->table} (" . implode(", ", $columns) . ") VALUES ({$placeholders})";

        $stmt = $this->connect()->prepare($sql);
        foreach ($data as $column => $value) {
            $stmt->bindValue(":{$column}", $value, $this->pdoType($value));
        }

        return $stmt->execute() ? $this->connect()->lastInsertId() : false;
    }

    public function update(int $id, array $data): bool
    {
        if (empty($data)) {
            return false;
        }

        $sets = [];
        foreach (array_keys($data) as $column) {
            $this->assertIdentifier($column);
            $sets[] = "{$column} = :{$column}";
        }

        $sql = "UPDATE {$this->table} SET " . implode(", ", $sets) . " WHERE {$this->primaryKey} = :__id";

        $stmt = $this->connect()->prepare($sql);
        foreach ($data as $column => $value) {
            $stmt->bindValue(":{$column}", $value, $this->pdoType($value));
        }
        $stmt->bindValue(":__id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ------------------------------------------------------------------
    // Query Builder (métodos encadenables)
    // ------------------------------------------------------------------

    public function select(string $fields): static
    {
        $this->select = $fields;
        return $this;
    }

    /**
     * Acepta dos formas:
     *  - ["prestamos.estado" => "PRESTADO"]       -> prestamos.estado = ?  (valor enlazado)
     *  - ["prestamos.stock >" => 0]                -> prestamos.stock > ?   (operador en la clave)
     *  - ["prestamos.activo = 1"]                  -> condición literal escrita por el programador
     *    (nunca coloques aquí datos que vengan del usuario).
     */
    public function where(array $conditions): static
    {
        foreach ($conditions as $key => $value) {
            if (is_int($key)) {
                $this->whereBuilder[] = (string) $value;
                continue;
            }

            $parts = preg_split('/\s+/', trim($key));
            $column = $parts[0];
            $operator = strtoupper($parts[1] ?? "=");

            $this->assertIdentifier($column);
            if (!in_array($operator, self::OPERATORS, true)) {
                throw new InvalidArgumentException("Operador no permitido: {$operator}");
            }

            if ($value === null) {
                $this->whereBuilder[] = $operator === "=" ? "{$column} IS NULL" : "{$column} IS NOT NULL";
                continue;
            }

            $this->whereBuilder[] = "{$column} {$operator} ?";
            $this->bindings[] = $value;
        }

        return $this;
    }

    public function orderBy(string $field, string $direction = "ASC"): static
    {
        $this->assertIdentifier($field);
        $direction = strtoupper($direction) === "DESC" ? "DESC" : "ASC";
        $this->orderBy = " ORDER BY {$field} {$direction}";
        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limit = " LIMIT " . max(1, $limit);
        return $this;
    }

    public function get(): array
    {
        $sql = "SELECT {$this->select} FROM {$this->table}";

        if (!empty($this->joins)) {
            $sql .= " " . implode(" ", $this->joins);
        }
        if (!empty($this->whereBuilder)) {
            $sql .= " WHERE " . implode(" AND ", $this->whereBuilder);
        }
        $sql .= $this->orderBy . $this->limit;

        $bindings = $this->bindings;
        $this->resetQuery();

        $stmt = $this->connect()->prepare($sql);
        foreach ($bindings as $i => $value) {
            $stmt->bindValue($i + 1, $value, $this->pdoType($value));
        }
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function first(): ?array
    {
        $rows = $this->limit(1)->get();
        return $rows[0] ?? null;
    }

    protected function resetQuery(): void
    {
        // Los JOIN ($this->joins) son permanentes: no se borran.
        $this->select = $this->defaultSelect;
        $this->whereBuilder = [];
        $this->bindings = [];
        $this->orderBy = "";
        $this->limit = "";
    }

    // ------------------------------------------------------------------
    // Utilidades internas
    // ------------------------------------------------------------------

    /** Evita inyección SQL en nombres de columnas (no se pueden enlazar con bindValue). */
    protected function assertIdentifier(string $name): void
    {
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*(\.[A-Za-z_][A-Za-z0-9_]*)?$/', $name)) {
            throw new InvalidArgumentException("Identificador no válido: {$name}");
        }
    }

    protected function pdoType(mixed $value): int
    {
        return match (true) {
            is_int($value) => PDO::PARAM_INT,
            is_bool($value) => PDO::PARAM_BOOL,
            $value === null => PDO::PARAM_NULL,
            default => PDO::PARAM_STR,
        };
    }
}
