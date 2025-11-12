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
    
    <link rel="stylesheet" href="../../../public/css/gestionAdministrador.css">
    <link rel="stylesheet" href="../../../public/css/dashboardAdministrador.css">
    <link rel="stylesheet" href="../../../public/css/footer.css">
</head>
<body>
    <header>
        <a href="administrador.php" class="logo">BibliONLINE</a>
        <ul class="navlist">
            <li><a href="panelGestionAdministrador.php" class="lnk">Gestion de usuarios</a></li>
            <li><a href="#" class="lnk">Subir contenido</a></li>
            <li><a href="#" class="lnk">Notificaciones</a></li>
            <li><a href="#" class="lnk">Mi cuenta</a></li>
            <li><a href="" class="lnk" id="btnCerrarSesion">Cerrar Sesion</a></li>
        </ul>

        <div class="bx bx-menu" id="menu-icon"></div>

    </header>

    <div class="wrapper">
        <div class="main-container">
            <div class="content1">
                <p>Generar copia de seguridad</p>
                <a id="btnBackup" class="btnGenerar">Crear</a>
                <p>Restaurar datos del sistema</p>
                <p>Seleccione un archivo .bak para realizar la restauracion de datos del sistema</p>
                    <form class="formularioDB" id="formularioDB">
                        <input type="file" id="inputArchivoDB" name="inputArchivoDB" accept=".bak" required>
                        <input type="submit" value="Restaurar" class="btnGenerar" id="btnRestaurarDB">
                    </form>
                <p>Generar Reporte general</p>
                <a id="" class="btnGenerar">Crear</a>
            </div>
            <div class="content2">
                <div class="subcontent1">
                    <p>Gestionar usuarios (editar, eliminar)</p>
                    <br>
                    <a href="consultarAdministrador.php" class="btnGenerar">Gestionar</a>
                </div>
                
                </div>
            </div>
        </div>
    </div>


    <script>window.csrfToken = '<?php echo $csrf; ?>';</script>
    <script src="../../../public/js/dashboardAdministrador.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>

<?php include '../footer.php'; ?>
