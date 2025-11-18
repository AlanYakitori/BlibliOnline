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
    
    <link rel="stylesheet" href="../../../public/css/gestionDocente.css">
    <link rel="stylesheet" href="../../../public/css/dashboardAdministrador.css">
    <link rel="stylesheet" href="../../../public/css/dashboardDocente.css">
    <link rel="stylesheet" href="../../../public/css/footer.css">
</head>
<body>
    <header>
        <a href="docente.php" class="logo">BibliONLINE</a>
        <ul class="navlist">
            <li><a href="panelGestionGrupos.php" class="lnk">Gestion de usuarios</a></li>
            <li><a href="panelGestionContenidoDocente.php" class="lnk">Subir contenido</a></li>
            <li><a href="#" class="lnk">Notificaciones</a></li>
            <li><a href="perfil.php" class="lnk">Mi cuenta</a></li>
            <li><a href="" class="lnk" id="btnCerrarSesion">Cerrar Sesion</a></li>
        </ul>

        <div class="bx bx-menu" id="menu-icon"></div>

    </header>

    <div class="wrapper">
        <div class="main-container">
            <div class="content1">
                <p>Crear un nuevo grupo</p>
                <p>Para crear un nuevo grupo ingrese el un nombre y despues presione el boton enviar</p>
                    <form class="formularioGrupo" id="formularioGrupo">
                        <input type="text" id="inputNombreGrupo">
                        <input type="submit" value="Crear Nuevo Grupo" class="btnGenerar" id="btnCrearGrupo">
                    </form>
                <div id="contenedorActualizarGrupos" class="contenedor-grupos" style="margin-top: 20px;"></div>
                <p>Generar Reporte general</p>
                <a id="" class="btnGenerar">Crear</a>
            </div>
            <div class="content2">
                <div class="subcontent1">
                    <p>Mis grupos</p>
                    <br>
                    <a id="btnGrupos" class="btnGenerar">Ver</a>
                    
                    <!-- Contenedor para mostrar los grupos -->
                    <div id="contenedorGrupos" class="contenedor-grupos" style="margin-top: 20px;"></div>
                </div>
                
                </div>
            </div>
        </div>
    </div>


    <script>window.csrfToken = '<?php echo $csrf; ?>';</script>
    <script src="../../../public/js/dashboardDocente.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>

<?php include '../footer.php'; ?>
