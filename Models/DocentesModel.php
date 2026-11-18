<?php

class DocentesModel extends Model
{
    protected string $table = "docente";

    /** Relación docente.id_facultad → facultad.id (JOIN permanente del ORM). */
    protected array $joins = [
        "JOIN facultad ON facultad.id = docente.id_facultad",
    ];

    /** Columnas del docente + datos de su facultad (alias para no pisar id/estado). */
    protected string $defaultSelect = "docente.*, facultad.nombre AS facultad_nombre, facultad.abreviatura AS facultad_abreviatura";

    public const CATEGORIAS = ["AUXILIAR", "ASOCIADO", "PRINCIPAL"];
    public const DEDICACIONES = ["DEDICACION EXCLUSIVA-DE", "TIEMPO COMPLETO-TC", "TIEMPO PARCIAL-TP"];

    public function __construct()
    {
        parent::__construct();
    }

    /** REQ-05 / REQ-06: docentes activos con su facultad. */
    public function activos(): array
    {
        return $this->where(["docente.estado" => 1])
            ->orderBy("docente.apellidos", "ASC")
            ->get();
    }

    /** Docente activo por id, con datos de su facultad. */
    public function activo(int $id): array|false
    {
        return $this->where(["docente.id" => $id, "docente.estado" => 1])->first() ?? false;
    }

    /** ¿Otro docente activo ya usa este DNI? */
    public function dniEnUso(string $dni, int $exceptoId = 0): bool
    {
        return $this->select("docente.id")
            ->where(["docente.dni" => $dni, "docente.estado" => 1, "docente.id <>" => $exceptoId])
            ->first() !== null;
    }

    /** REQ-12: eliminación lógica (estado 1 → 0). */
    public function eliminarLogico(int $id): bool
    {
        return $this->activo($id) !== false && $this->update($id, ["estado" => 0]);
    }
}
