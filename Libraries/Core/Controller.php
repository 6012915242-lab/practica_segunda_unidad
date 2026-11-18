<?php

class Controller
{
    protected Views $views;
    protected ?Model $model = null;

    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (empty($_SESSION["csrf_token"])) {
            $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
        }

        $this->views = new Views();
        $this->loadModel();
    }

    private function loadModel(): void
    {
        $modelName = str_replace("Controller", "Model", get_class($this));
        $modelPath = "Models/{$modelName}.php";

        if (file_exists($modelPath)) {
            require_once $modelPath;
            $this->model = new $modelName();
        }
    }

    /** Redirige dentro del proyecto y detiene la ejecución (patrón PRG). */
    protected function redirect(string $ruta = ""): never
    {
        header("Location: " . BASE_URL . "/" . ltrim($ruta, "/"));
        exit;
    }

    /** Guarda un mensaje que se mostrará una sola vez después de redirigir. */
    protected function flash(string $texto, string $tipo = "ok"): void
    {
        $_SESSION["flash"] = ["tipo" => $tipo, "texto" => $texto];
    }

    /** Las operaciones que modifican datos solo se aceptan por POST con token CSRF válido. */
    protected function exigirPost(string $volverA): void
    {
        $token = $_POST["csrf_token"] ?? "";
        if (($_SERVER["REQUEST_METHOD"] ?? "GET") !== "POST"
            || !is_string($token)
            || !hash_equals($_SESSION["csrf_token"], $token)) {
            $this->flash("Solicitud no válida. Use los botones del sistema.", "error");
            $this->redirect($volverA);
        }
    }

    /** Convierte el parámetro de la URL en un id entero positivo (0 si no es válido). */
    protected function idValido(mixed $id): int
    {
        return (int) filter_var($id, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
    }
}
