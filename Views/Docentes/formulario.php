<?php require "Views/Template/header.php"; ?>
<?php $d = $data["docente"]; $err = $data["errores"]; ?>

<h1><?= htmlspecialchars($data["page_title"]) ?></h1>

<?php if (empty($data["facultades"])): ?>
    <div class="alerta error">No hay facultades activas. Registre una facultad primero.</div>
<?php endif; ?>

<form method="POST" action="<?= BASE_URL ?>/<?= htmlspecialchars($data["accion"]) ?>" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION["csrf_token"]) ?>">

    <div class="campo">
        <label for="id_facultad">Facultad</label>
        <select id="id_facultad" name="id_facultad" required>
            <option value="">-- Seleccione --</option>
            <?php foreach ($data["facultades"] as $f): ?>
                <option value="<?= (int) $f["id"] ?>" <?= (int) $d["id_facultad"] === (int) $f["id"] ? "selected" : "" ?>>
                    <?= htmlspecialchars($f["abreviatura"] . " - " . $f["nombre"]) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($err["id_facultad"])): ?><span class="err"><?= htmlspecialchars($err["id_facultad"]) ?></span><?php endif; ?>
    </div>

    <div>
        <label for="dni">DNI</label>
        <input type="text" id="dni" name="dni" inputmode="numeric" pattern="\d{8}" maxlength="8" required value="<?= htmlspecialchars($d["dni"]) ?>">
        <?php if (isset($err["dni"])): ?><span class="err"><?= htmlspecialchars($err["dni"]) ?></span><?php endif; ?>
    </div>

    <div>
        <label for="apellidos">Apellidos</label>
        <input type="text" id="apellidos" name="apellidos" maxlength="100" required value="<?= htmlspecialchars($d["apellidos"]) ?>">
        <?php if (isset($err["apellidos"])): ?><span class="err"><?= htmlspecialchars($err["apellidos"]) ?></span><?php endif; ?>
    </div>

    <div>
        <label for="nombres">Nombres</label>
        <input type="text" id="nombres" name="nombres" maxlength="100" required value="<?= htmlspecialchars($d["nombres"]) ?>">
        <?php if (isset($err["nombres"])): ?><span class="err"><?= htmlspecialchars($err["nombres"]) ?></span><?php endif; ?>
    </div>

    <div>
        <label for="correo">Correo</label>
        <input type="email" id="correo" name="correo" maxlength="150" required value="<?= htmlspecialchars($d["correo"]) ?>">
        <?php if (isset($err["correo"])): ?><span class="err"><?= htmlspecialchars($err["correo"]) ?></span><?php endif; ?>
    </div>

    <div>
        <label for="categoria">Categoría</label>
        <select id="categoria" name="categoria" required>
            <option value="">-- Seleccione --</option>
            <?php foreach ($data["categorias"] as $c): ?>
                <option value="<?= htmlspecialchars($c) ?>" <?= $d["categoria"] === $c ? "selected" : "" ?>><?= htmlspecialchars($c) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($err["categoria"])): ?><span class="err"><?= htmlspecialchars($err["categoria"]) ?></span><?php endif; ?>
    </div>

    <div>
        <label for="dedicacion">Dedicación</label>
        <select id="dedicacion" name="dedicacion" required>
            <option value="">-- Seleccione --</option>
            <?php foreach ($data["dedicaciones"] as $ded): ?>
                <option value="<?= htmlspecialchars($ded) ?>" <?= $d["dedicacion"] === $ded ? "selected" : "" ?>><?= htmlspecialchars($ded) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($err["dedicacion"])): ?><span class="err"><?= htmlspecialchars($err["dedicacion"]) ?></span><?php endif; ?>
    </div>

    <div>
        <button class="btn" type="submit">Guardar</button>
        <a class="btn btn-gris" href="<?= BASE_URL ?>/docentes">Cancelar</a>
    </div>
</form>

<?php require "Views/Template/footer.php"; ?>
