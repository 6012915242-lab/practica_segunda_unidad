<?php
$flash = $_SESSION["flash"] ?? null;
unset($_SESSION["flash"]);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($data["page_title"] ?? "Gestión académica") ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/Assets/css/style.css">
</head>
<body>
<header>
    <a href="<?= BASE_URL ?>/home">Gestión académica</a>
    <nav>
        <a href="<?= BASE_URL ?>/facultades">Facultades</a>
        <a href="<?= BASE_URL ?>/docentes">Docentes</a>
    </nav>
</header>
<main >
<?php if ($flash): ?>
    <div class="alerta <?= $flash["tipo"] === "error" ? "error" : "ok" ?>"><?= htmlspecialchars($flash["texto"]) ?></div>
<?php endif; ?>
