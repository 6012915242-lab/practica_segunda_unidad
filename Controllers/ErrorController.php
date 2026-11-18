<?php

http_response_code(404);
$data["page_title"] = "Página no encontrada";
require "Views/Template/header.php";
?>
<h1>404</h1>
<p>La ruta solicitada no existe.</p>
<p><a href="<?= BASE_URL ?>/home">Volver al inicio</a></p>
<?php require "Views/Template/footer.php"; ?>
