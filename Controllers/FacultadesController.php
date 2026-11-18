<?php

class FacultadesController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /** GET /facultades  (REQ-01) */
    public function index(): void
    {
        $data["page_title"] = "Facultades";
        $data["facultades"] = $this->model->activas();
        $this->views->render($this, "index", $data);
    }

    /** GET /facultades/crear */
    public function crear(): void
    {
        $this->mostrarFormulario("Registrar facultad", "facultades/guardar");
    }

    /** POST /facultades/guardar  (REQ-02) */
    public function guardar(): void
    {
        $this->exigirPost("facultades");
        $input = $this->leerFormulario();
        $errores = $this->validar($input);

        if ($errores) {
            $this->mostrarFormulario("Registrar facultad", "facultades/guardar", $input, $errores);
            return;
        }

        $this->model->insert($input + ["estado" => 1]);
        $this->flash("Facultad registrada correctamente.");
        $this->redirect("facultades");
    }

    /** GET /facultades/editar/{id} */
    public function editar($id = null): void
    {
        $id = $this->idValido($id);
        $facultad = $id ? $this->model->activa($id) : false;

        if (!$facultad) {
            $this->flash("La facultad no existe o fue eliminada.", "error");
            $this->redirect("facultades");
        }

        $this->mostrarFormulario("Editar facultad", "facultades/actualizar/{$id}", $facultad);
    }

    /** POST /facultades/actualizar/{id}  (REQ-03) */
    public function actualizar($id = null): void
    {
        $this->exigirPost("facultades");
        $id = $this->idValido($id);

        if (!$id || !$this->model->activa($id)) {
            $this->flash("La facultad no existe o fue eliminada.", "error");
            $this->redirect("facultades");
        }

        $input = $this->leerFormulario();
        $errores = $this->validar($input);

        if ($errores) {
            $this->mostrarFormulario("Editar facultad", "facultades/actualizar/{$id}", $input, $errores);
            return;
        }

        $this->model->update($id, $input);
        $this->flash("Facultad actualizada correctamente.");
        $this->redirect("facultades");
    }

    /** POST /facultades/eliminar/{id}  (REQ-04: eliminación lógica) */
    public function eliminar($id = null): void
    {
        $this->exigirPost("facultades");
        $id = $this->idValido($id);

        $id && $this->model->eliminarLogico($id)
            ? $this->flash("Facultad eliminada (estado = 0).")
            : $this->flash("No se pudo eliminar: la facultad no existe o ya estaba eliminada.", "error");

        $this->redirect("facultades");
    }

    // ------------------------------------------------------------------

    private function leerFormulario(): array
    {
        return [
            "nombre"      => trim($_POST["nombre"] ?? ""),
            "abreviatura" => mb_strtoupper(trim($_POST["abreviatura"] ?? "")),
        ];
    }

    private function validar(array $input): array
    {
        $errores = [];
        $largo = mb_strlen($input["nombre"]);
        if ($largo < 3 || $largo > 120) {
            $errores["nombre"] = "El nombre debe tener entre 3 y 120 caracteres.";
        }
        if (!preg_match('/^[A-ZÁÉÍÓÚÑ]{2,20}$/u', $input["abreviatura"])) {
            $errores["abreviatura"] = "La abreviatura debe tener de 2 a 20 letras, sin espacios.";
        }
        return $errores;
    }

    private function mostrarFormulario(string $titulo, string $accion, array $facultad = [], array $errores = []): void
    {
        $data["page_title"] = $titulo;
        $data["accion"] = $accion;
        $data["facultad"] = $facultad + ["nombre" => "", "abreviatura" => ""];
        $data["errores"] = $errores;
        $this->views->render($this, "formulario", $data);
    }
}
