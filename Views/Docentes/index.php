<?php require "Views/Template/header.php"; ?>

<div class="encabezado">
    <h1>Docentes activos</h1>
    <a class="btn" href="<?= BASE_URL ?>/docentes/crear">+ Nuevo docente</a>
</div>

<?php if (empty($data["docentes"])): ?>
    <p>No hay docentes activos.</p>
<?php else: ?>
    <table border = "1px">
        <thead>
            <tr>
                <th>Facultad</th><th>DNI</th><th>Apellidos y nombres</th><th>Correo</th>
                <th>Categoría</th><th>Dedicación</th><th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($data["docentes"] as $d): ?>
            <?php $id = (int) $d["id"]; ?>
            <tr>
                <td title="<?= htmlspecialchars($d["facultad_nombre"]) ?>"><?= htmlspecialchars($d["facultad_abreviatura"]) ?></td>
                <td><?= htmlspecialchars($d["dni"]) ?></td>
                <td><?= htmlspecialchars($d["apellidos"] . ", " . $d["nombres"]) ?></td>
                <td><?= htmlspecialchars($d["correo"]) ?></td>
                <td><?= htmlspecialchars($d["categoria"]) ?></td>
                <td><?= htmlspecialchars($d["dedicacion"]) ?></td>
                <td class="acciones">
                    <a class="btn btn-gris" href="<?= BASE_URL ?>/docentes/editar/<?= $id ?>">Editar</a>
                    <form method="POST" action="<?= BASE_URL ?>/docentes/eliminar/<?= $id ?>" data-confirmar="¿Eliminar al docente <?= htmlspecialchars($d["apellidos"]) ?>?">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION["csrf_token"]) ?>">
                        <button class="btn btn-rojo" type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require "Views/Template/footer.php"; ?>
