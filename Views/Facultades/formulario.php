<?php require "Views/Template/header.php"; ?>
<?php $f = $data["facultad"]; $err = $data["errores"]; ?>

<h1><?= htmlspecialchars($data["page_title"]) ?></h1>

<form method="POST" action="<?= BASE_URL ?>/<?= htmlspecialchars($data["accion"]) ?>" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION["csrf_token"]) ?>">

    <div>
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" maxlength="120" required value="<?= htmlspecialchars($f["nombre"]) ?>">
        <?php if (isset($err["nombre"])): ?><span class="err"><?= htmlspecialchars($err["nombre"]) ?></span><?php endif; ?>
    </div>

    <div>
        <label for="abreviatura">Abreviatura</label>
        <input type="text" id="abreviatura" name="abreviatura" maxlength="20" required value="<?= htmlspecialchars($f["abreviatura"]) ?>">
        <?php if (isset($err["abreviatura"])): ?><span class="err"><?= htmlspecialchars($err["abreviatura"]) ?></span><?php endif; ?>
    </div>

    <div>
        <button type="submit">Guardar</button>
        <a href="<?= BASE_URL ?>/facultades">Cancelar</a>
    </div>
</form>

<?php require "Views/Template/footer.php"; ?>
