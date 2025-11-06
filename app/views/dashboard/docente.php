<?php
require_once __DIR__ . '/../../../config/session.php';
// Proteger la página para que solo docentes puedan acceder
protegerPagina(['docente']);
$csrf = obtenerCSRFToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Docente</title>
</head>
<body>
    <h1>Docente</h1>

    <button id="btnCerrarSesion">Cerrar sesión</button>

    <script>window.csrfToken = '<?php echo $csrf; ?>';</script>
    <script src="../../../public/js/dashboardDocente.js"></script>
</body>
</html>