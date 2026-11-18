<?php

require_once "Models/FacultadesModel.php";

class DocentesController extends Controller
{
    private FacultadesModel $facultades;

    public function __construct()
    {
        parent::__construct();
        $this->facultades = new FacultadesModel();
    }

    /** GET /docentes  (REQ-05, REQ-06) */
    public function index(): void
    {
        $data["page_title"] = "Docentes";
        $data["docentes"] = $this->model->activos();
        $this->views->render($this, "index", $data);
    }

    /** GET /docentes/crear */
    public function crear(): void
    {
        $this->mostrarFormulario("Registrar docente", "docentes/guardar");
    }

    /** POST /docentes/guardar  (REQ-07 a REQ-10) */
    public function guardar(): void
    {
        $this->exigirPost("docentes");
        $input = $this->leerFormulario();
        $errores = $this->validar($input);

        if ($errores) {
            $this->mostrarFormulario("Registrar docente", "docentes/guardar", $input, $errores);
            return;
        }

        $this->model->insert($input + ["estado" => 1]);
        $this->flash("Docente registrado correctamente.");
        $this->redirect("docentes");
    }

    /** GET /docentes/editar/{id} */
    public function editar($id = null): void
    {
        $id = $this->idValido($id);
        $docente = $id ? $this->model->activo($id) : false;

        if (!$docente) {
            $this->flash("El docente no existe o fue eliminado.", "error");
            $this->redirect("docentes");
        }

        $this->mostrarFormulario("Editar docente", "docentes/actualizar/{$id}", $docente);
    }

    /** POST /docentes/actualizar/{id}  (REQ-11) */
    public function actualizar($id = null): void
    {
        $this->exigirPost("docentes");
        $id = $this->idValido($id);

        if (!$id || !$this->model->activo($id)) {
            $this->flash("El docente no existe o fue eliminado.", "error");
            $this->redirect("docentes");
        }

        $input = $this->leerFormulario();
        $errores = $this->validar($input, $id);

        if ($errores) {
            $this->mostrarFormulario("Editar docente", "docentes/actualizar/{$id}", $input, $errores);
            return;
        }

        $this->model->update($id, $input);
        $this->flash("Docente actualizado correctamente.");
        $this->redirect("docentes");
    }

    /** POST /docentes/eliminar/{id}  (REQ-12: eliminación lógica) */
    public function eliminar($id = null): void
    {
        $this->exigirPost("docentes");
        $id = $this->idValido($id);

        $id && $this->model->eliminarLogico($id)
            ? $this->flash("Docente eliminado (estado = 0).")
            : $this->flash("No se pudo eliminar: el docente no existe o ya estaba eliminado.", "error");

        $this->redirect("docentes");
    }

    // ------------------------------------------------------------------

    private function leerFormulario(): array
    {
        return [
            "id_facultad" => (int) ($_POST["id_facultad"] ?? 0),
            "dni"         => trim($_POST["dni"] ?? ""),
            "apellidos"   => trim($_POST["apellidos"] ?? ""),
            "nombres"     => trim($_POST["nombres"] ?? ""),
            "correo"      => mb_strtolower(trim($_POST["correo"] ?? "")),
            "categoria"   => trim($_POST["categoria"] ?? ""),
            "dedicacion"  => trim($_POST["dedicacion"] ?? ""),
        ];
    }

    private function validar(array $input, int $exceptoId = 0): array
    {
        $errores = [];

        // REQ-07: la facultad debe existir y estar activa.
        if ($input["id_facultad"] < 1 || !$this->facultades->activa($input["id_facultad"])) {
            $errores["id_facultad"] = "Seleccione una facultad activa.";
        }

        if (!preg_match('/^\d{8}$/', $input["dni"])) {
            $errores["dni"] = "El DNI debe tener exactamente 8 dígitos.";
        } elseif ($this->model->dniEnUso($input["dni"], $exceptoId)) {
            $errores["dni"] = "Ya existe un docente activo con ese DNI.";
        }

        foreach (["apellidos" => "Los apellidos", "nombres" => "Los nombres"] as $campo => $etiqueta) {
            $largo = mb_strlen($input[$campo]);
            if ($largo < 2 || $largo > 100) {
                $errores[$campo] = "{$etiqueta} deben tener entre 2 y 100 caracteres.";
            }
        }

        if (!filter_var($input["correo"], FILTER_VALIDATE_EMAIL) || mb_strlen($input["correo"]) > 150) {
            $errores["correo"] = "Ingrese un correo válido (máximo 150 caracteres).";
        }

        // REQ-09 y REQ-10: solo valores permitidos (lista blanca).
        if (!in_array($input["categoria"], DocentesModel::CATEGORIAS, true)) {
            $errores["categoria"] = "Seleccione una categoría válida.";
        }
        if (!in_array($input["dedicacion"], DocentesModel::DEDICACIONES, true)) {
            $errores["dedicacion"] = "Seleccione una dedicación válida.";
        }

        return $errores;
    }

    private function mostrarFormulario(string $titulo, string $accion, array $docente = [], array $errores = []): void
    {
        $data["page_title"] = $titulo;
        $data["accion"] = $accion;
        $data["docente"] = $docente + [
            "id_facultad" => 0, "dni" => "", "apellidos" => "", "nombres" => "",
            "correo" => "", "categoria" => "", "dedicacion" => "",
        ];
        $data["facultades"] = $this->facultades->activas();     // REQ-08: control de selección
        $data["categorias"] = DocentesModel::CATEGORIAS;
        $data["dedicaciones"] = DocentesModel::DEDICACIONES;
        $data["errores"] = $errores;
        $this->views->render($this, "formulario", $data);
    }
}
