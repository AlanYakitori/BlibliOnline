<?php
require_once __DIR__ . '/../../../config/session.php';

protegerPagina(['alumno']);
$csrf = obtenerCSRFToken();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Grupo - BibliONLINE</title>

    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.7.0/fonts/remixicon.css" rel="stylesheet">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../../../public/css/dashboardAdministrador.css">
    <link rel="stylesheet" href="../../../public/css/dashboardAlumno.css">
    <link rel="stylesheet" href="../../../public/css/footer.css">
</head>
<body>
    <header>
        <a href="alumno.php" class="logo">BibliONLINE</a>
        <ul class="navlist">
            <li><a href="panelGestionAlumno.php" class="lnk">Ver Grupo</a></li>
            <li><a href="panelGestionContenidoAlumno.php" class="lnk">Subir contenido</a></li>
            <li><a href="notificacionesAlumno.php" class="lnk">Notificaciones</a></li>
            <li><a href="perfilAlumno.php" class="lnk">Mi cuenta</a></li>
            <li><a href="" class="lnk" id="btnCerrarSesion">Cerrar Sesión</a></li>
        </ul>

        <div class="bx bx-menu" id="menu-icon"></div>
    </header>

    <div class="wrapper">
        <div class="main-container">
            <div class="content1">
                <center><h2>Mi Grupo</h2></center>
                
                <!-- Contenedor para mostrar la información del grupo -->
                <div id="contenedorInfoGrupo" class="contenedor-grupos" style="margin-top: 20px;"></div>
            </div>
        </div>
    </div>

    <script>window.csrfToken = '<?php echo $csrf; ?>';</script>
    <script src="../../../public/js/dashboardAlumno.js"></script>
    <script src="../../../public/js/dashboardAdministrador.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>

<?php include '../footer.php'; ?>