<?php
// (1) SEGURIDAD: Se ejecuta en el servidor PRIMERO
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
    <link rel="stylesheet" href="../../../public/css/footer.css">
    <link rel="stylesheet" href="../../../public/css/consulta.css">
</head>
<body>

    <header>
        <a href="administrador.php" class="logo">BibliONLINE</a>
        <ul class="navlist">
            <li><a href="panelGestionAdministrador.php">Herramientas</a></li>
            <li><a href="panelGestionContenidoAdministrador.php">Subir contenido</a></li>
            <li><a href="perfil.php">Notificaciones</a></li>
            <li><a href="#">Mi cuenta</a></li>
            <li><a href="#" class="lnk" id="btnCerrarSesion">Cerrar Sesion</a></li>
        </ul>
        <div class="bx bx-menu" id="menu-icon"></div>
    </header>

    <br><br><br><br><br><br>

    <div class="container1">
    <div class="container">
        <h2>Gestión de Usuarios</h2>
        <table class="">
            <thead class="">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tabla-usuarios-body">
                <tr>
                    <td colspan="8" class=""></td>
                </tr>
            </tbody>
        </table>
    </div>
    </div>

    <br><br><br>

    <?php include '../footer.php'; ?>

    
    <script>
        window.csrfToken = '<?php echo $csrf; ?>';
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="../../../public/js/consultarAdministrador.js"></script> 
    
    <script src="../../../public/js/dashboardAdministrador.js"></script> 

</body>
</html>