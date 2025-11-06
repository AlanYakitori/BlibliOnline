<?php
require_once __DIR__ . '/../../../config/session.php';
// Proteger la página para que solo administradores puedan acceder
protegerPagina(['administrador']);
$csrf = obtenerCSRFToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrador</title>
</head>
<body>
    <h1>Administrador</h1>

    <button id="btnCerrarSesion">Cerrar sesión</button>
    <button id="btnBackup">Crear copia de seguridad</button>

    <!-- Exponer CSRF token al JS de forma segura -->
    <script>window.csrfToken = '<?php echo $csrf; ?>';</script>
    <script src="../../../public/js/dashboardAdministrador.js"></script>
</body>
</html>