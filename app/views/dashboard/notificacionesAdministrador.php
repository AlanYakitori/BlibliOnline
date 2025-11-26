<?php
require_once __DIR__ . '/../../../config/session.php';

protegerPagina(['administrador']);
$csrf = obtenerCSRFToken();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.7.0/fonts/remixicon.css" rel="stylesheet">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../../../public/css/dashboardAdministrador.css">
    <link rel="stylesheet" href="../../../public/css/swiper.css">
    <link rel="stylesheet" href="../../../public/css/notificaciones.css">
</head>
<body>
    <header>
        <a href="administrador.php" class="logo">BibliONLINE</a>
        <ul class="navlist">
            <li><a href="panelGestionAdministrador.php" class="lnk active">Herramientas</a></li>
            <li><a href="panelGestionContenidoAdministrador.php" class="lnk">Subir Contenido</a></li>
            <li><a href="notificacionesAdministrador.php" class="lnk">Notificaciones</a></li>
            <li><a href="perfilAdministrador.php" class="lnk">Mi cuenta</a></li>
            <li><a href="#" class="lnk" id="btnCerrarSesion">Cerrar Sesión</a></li>
        </ul>
        <div class="bx bx-menu" id="menu-icon"></div>
    </header>

    <br><br>

    <!-- Contenido principal -->
    <main class="contenido-principal">
        <div class="contenedor-notificaciones">
            <div id="contenedorNotificaciones">
                <!-- Las notificaciones se cargarán aquí dinámicamente -->
                <div class="cargando">
                    <p>Cargando notificaciones...</p>
                </div>
            </div>
        </div>
    </main>



    <!-- Modal para motivo de rechazo de usuario -->
    <div id="modalRechazoUsuario" class="modal-rechazo-usuario" style="display: none;">
        <div class="modal-header">
            <h3>Rechazar Usuario</h3>
            <button onclick="cerrarModalRechazoUsuario()" class="btn-cerrar">&times;</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="usuarioIdRechazo" value="">
            <label for="motivoRechazoUsuario">Motivo del rechazo:</label>
            <textarea id="motivoRechazoUsuario" rows="4" placeholder="Explica por qué estás rechazando este usuario..."></textarea>
        </div>
        <div class="modal-footer">
            <button onclick="cerrarModalRechazoUsuario()" class="btn-cancelar">Cancelar</button>
            <button onclick="confirmarRechazoUsuario()" class="btn-confirmar">Confirmar Rechazo</button>
        </div>
    </div>

    <script>window.csrfToken = '<?php echo $csrf; ?>';</script>
    <script src="../../../public/js/notificaciones.js"></script>

    <script src="../../../public/js/dashboardAdministrador.js"></script>
    <script src="https://kit.fontawesome.com/b668f928a3.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
    
</body>
</html>