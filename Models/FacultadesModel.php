<?php

class FacultadesModel extends Model
{
    protected string $table = "facultad";

    public function __construct()
    {
        parent::__construct();
    }

    /** REQ-01: solo facultades activas. */
    public function activas(): array
    {
        return $this->where(["estado" => 1])->orderBy("nombre", "ASC")->get();
    }

    /** Facultad activa por id (false si no existe o está eliminada). */
    public function activa(int $id): array|false
    {
        return $this->where(["id" => $id, "estado" => 1])->first() ?? false;
    }

    /** REQ-04: eliminación lógica (estado 1 → 0), sin DELETE físico. */
    public function eliminarLogico(int $id): bool
    {
        return $this->activa($id) !== false && $this->update($id, ["estado" => 0]);
    }
}
