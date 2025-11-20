<?php
require_once __DIR__ . '/../../../config/session.php';

protegerPagina(['docente']);
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
</head>
<body>
    <header>
        <a href="docente.php" class="logo">BibliONLINE</a>
        <ul class="navlist">
            <li><a href="panelGestionGrupos.php" class="lnk">Herramientas</a></li>
            <li><a href="panelGestionContenidoDocente.php">Subir Contenido</a></li>
            <li><a href="notificacionesDocente.php">Notificaciones</a></li>
            <li><a href="perfilDocente.php">Mi cuenta</a></li>
            <li><a href="" class="lnk" id="btnCerrarSesion">Cerrar Sesion</a></li>
        </ul>
        <div class="bx bx-menu" id="menu-icon"></div>
    </header>


    <script>window.csrfToken = '<?php echo $csrf; ?>';</script>
    <script src="../../../public/js/dashboardDocente.js"></script>
    <script src="https://kit.fontawesome.com/b668f928a3.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
    <script src="../../../public/js/swiper.js"></script>
    <script src="../../../public/js/feed.js"></script>
</body>
</html>

