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

    <link rel="shortcut icon" href="/favicon.svg">       
    <link rel="icon" sizes="64x64" href="../../../public/media/book.svg">         
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon.svg">  

    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.7.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css"/>

    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../../../public/css/dashboardAdministrador.css">
    <link rel="stylesheet" href="../../../public/css/footer.css">
    <link rel="stylesheet" href="../../../public/css/swiper.css">

</head>
<body>
    <header>
        <a href="#" class="logo">BibliONLINE</a>
        <ul class="navlist">
            <li><a href="panelGestionAdministrador.php">Herramientas</a></li>
            <li><a href="panelGestionContenidoAdministrador.php">Subir Contenido</a></li>
            <li><a href="#">Notificaciones</a></li>
            <li><a href="perfilAdministrador.php">Mi cuenta</a></li>
            <li><a href="" class="lnk" id="btnCerrarSesion">Cerrar Sesion</a></li>
        </ul>

        <div class="bx bx-menu" id="menu-icon"></div>

    </header>

<br><br><br><br>
<br><br>
    <center><h1 id="nombreBienvenida"></h1></center>
    <div class="subTitulo">
        <h2>Recomendacion segun tus gustos</h2>
    </div>
    <div class="swiper">
        <div class="swiper-wrapper" id="carrusel-wrapper">
        </div>

        <div class="swiper-button-next"></div>
    </div>
    <br><br><br><br><br><br>
    <div id="modal-detalle" class="modal-overlay">
        <div class="modal-contenido">
            
            <span class="modal-cerrar">&times;</span>
            
            <h3 id="modal-titulo"></h3>
            
            <p id="modal-desc"></p>

            <div class="modal-enlace">
                <strong>Recurso:</strong>
                <a id="modal-url-link" href="#" target="_blank" rel="noopener noreferrer"></a>
            </div>

            <div class="modal-acciones">
    
            <div class="valoracion">
                <i class="fa-regular fa-star" data-valor="1"></i>
                <i class="fa-regular fa-star" data-valor="2"></i>
                <i class="fa-regular fa-star" data-valor="3"></i>
                <i class="fa-regular fa-star" data-valor="4"></i>
                <i class="fa-regular fa-star" data-valor="5"></i>
            </div>
            
            <button id="btn-favorito" class="btn-favorito">
                <i class="fa-regular fa-heart"></i>
                <span>Agregar a Favoritos</span>
            </button>

        </div>
            
        </div>
    </div>


<?php include '../footer.php'; ?>

    <script>window.csrfToken = '<?php echo $csrf; ?>';</script>
    <script src="../../../public/js/dashboardAdministrador.js"></script>
    <script src="https://kit.fontawesome.com/b668f928a3.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
    <script src="../../../public/js/swiper.js"></script>
</body>
</html>


