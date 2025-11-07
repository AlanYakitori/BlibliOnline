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
    <link rel="stylesheet" href="../../../public/css/footer.css">
    <link rel="stylesheet" href="../../../public/css/dashboardAdministrador.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.7.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>Home</title>
</head>
<body>
    <header>
        <a href="" class="logo">BibliONLINE</a>
        <ul class="navlist">
            <li><a href="#">Gestion de usuarios</a></li>
            <li><a href="#">Subir contenido</a></li>
            <li><a href="#">Notificaciones</a></li>
            <li><a href="#">Mi cuenta</a></li>
            <li><button id="btnCerrarSesion" class="btn"><a href="">Cerrar Sesion</a></button></li>
        </ul>

        <div class="bx bx-menu" id="menu-icon"></div>

    </header>

<br><br><br><br>
<br><br><br><br>
<center><h1 id="nombreBienvenida"></h1></center>
<br><br><br><br>
<br><br><br><br>
<br><br><br><br>
<br><br><br><br>
<center><h3>Aqui va el contenido</h3></center>
<br><br><br><br>
<br><br><br><br>
<br><br><br>
    <button id="btnBackup">Crear copia de seguridad</button>

    <script>window.csrfToken = '<?php echo $csrf; ?>';</script>
    <script src="../../../public/js/dashboardAdministrador.js"></script>
</body>
</html>

<?php include '../footer.php'; ?>
