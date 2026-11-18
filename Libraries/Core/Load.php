<?php

$controllerFile = "Controllers/{$controller}Controller.php";

if (!file_exists($controllerFile)) {
    require_once "Controllers/ErrorController.php";
    exit;
}

require_once $controllerFile;
$className = "{$controller}Controller";
$object = new $className();

// is_callable descarta métodos privados/protegidos; "__" descarta métodos mágicos.
if (!is_callable([$object, $method]) || str_starts_with($method, "__")) {
    require_once "Controllers/ErrorController.php";
    exit;
}

$param === null ? $object->$method() : $object->$method($param);
