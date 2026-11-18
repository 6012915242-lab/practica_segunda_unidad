<?php require "Views/Template/header.php"; ?>

<div>
    <h1>Facultades activas</h1>
    <a class="btn" href="<?= BASE_URL ?>/facultades/crear">+ Nueva facultad</a>
</div>

<?php if (empty($data["facultades"])): ?>
    <p>No hay facultades activas.</p>
<?php else: ?>
    <table border ="1px" >
        <thead>
            <tr><th>#</th><th>Nombre</th><th>Abreviatura</th><th>Acciones</th></tr>
        </thead>
        <tbody>
        <?php foreach ($data["facultades"] as $f): ?>
            <?php $id = (int) $f["id"]; ?>
            <tr>
                <td><?= $id ?></td>
                <td><?= htmlspecialchars($f["nombre"]) ?></td>
                <td><?= htmlspecialchars($f["abreviatura"]) ?></td>
                <td class="acciones">
                    <a class="btn btn-gris" href="<?= BASE_URL ?>/facultades/editar/<?= $id ?>">Editar</a>
                    <form method="POST" action="<?= BASE_URL ?>/facultades/eliminar/<?= $id ?>" data-confirmar="¿Eliminar la facultad <?= htmlspecialchars($f["abreviatura"]) ?>?">
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
