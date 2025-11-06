<?php
require_once __DIR__ . '/../../../config/session.php';
// Proteger la página para que solo alumnos puedan acceder
protegerPagina(['alumno']);
$csrf = obtenerCSRFToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Alumno</title>
</head>
<body>
    <h1>Alumno</h1>

    <button id="btnCerrarSesion">Cerrar sesión</button>

    <script>window.csrfToken = '<?php echo $csrf; ?>';</script>
    <script src="../../../public/js/dashboardAlumno.js"></script>
</body>
</html>