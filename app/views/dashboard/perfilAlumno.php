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
    <title>Mi Perfil - Estudiante | BibliONLINE</title>

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
    <link rel="stylesheet" href="../../../public/css/dashboardAlumno.css">
    <link rel="stylesheet" href="../../../public/css/footer.css">
    <link rel="stylesheet" href="../../../public/css/perfil.css"> 
</head>
<body>
    <header>
        <a href="alumno.php" class="logo">BibliONLINE</a>
        <ul class="navlist" id="navListAlumno">
            <!-- La navegación será generada dinámicamente por JavaScript -->
        </ul>

        <div class="bx bx-menu" id="menu-icon"></div>
    </header>

    <br><br><br><br><br><br>

    <div class="perfil-container">

        <div class="perfil-card">
            <h2>Mis Datos</h2>
            <div class="datos-vista">
                <p><strong>Nombre:</strong> <span id="vista-nombre">Cargando...</span></p>
                <p><strong>Apellidos:</strong> <span id="vista-apellidos"></span></p>
                <p><strong>Correo:</strong> <span id="vista-correo"></span></p>
                <p><strong>Teléfono:</strong> <span id="vista-telefono"></span></p>
            </div>
            
            <button id="btn-abrir-modal" class="btn-actualizar">Actualizar Datos</button>
        </div>

        <div class="perfil-card">
            <h2>Mis Favoritos</h2>
            <div id="contenedor-favoritos">
                <p>Cargando favoritos...</p>
            </div>
        </div>

    </div> <div id="modal-actualizar" class="modal-overlay">
        <div class="modal-contenido">
            <span class="modal-cerrar">&times;</span>
            
            <h3>Actualizar Mis Datos</h3>
            
            <form id="form-actualizar-datos">
                
                <input type="hidden" id="perfil-id">

                <div class="form-grupo">
                    <label for="perfil-nombre-input">Nombre:</label>
                    <input type="text" id="perfil-nombre-input" name="nombre" required>
                </div>
                
                <div class="form-grupo">
                    <label for="perfil-apellidos-input">Apellidos:</label>
                    <input type="text" id="perfil-apellidos-input" name="apellidos" required>
                </div>

                <div class="form-grupo">
                    <label for="perfil-correo-input">Correo:</label>
                    <input type="email" id="perfil-correo-input" name="correo" required>
                </div>

                <div class="form-grupo">
                    <label for="perfil-telefono-input">Teléfono:</label>
                    <input type="tel" id="perfil-telefono-input" name="telefono">
                </div>

                <hr>
                
                <div class="form-grupo">
                    <label for="perfil-pass1">Nueva Contraseña (dejar en blanco para no cambiar):</label>
                    <input type="password" id="perfil-pass1">
                </div>
                
                <div class="form-grupo">
                    <label for="perfil-pass2">Confirmar Contraseña:</label>
                    <input type="password" id="perfil-pass2">
                </div>

                <button type="submit" class="btn-actualizar">Guardar Cambios</button>
                <div id="mensaje-perfil" class="mensajeGeneral"></div>
            </form>

        </div>
    </div>
    <br><br><br>


    <script>window.csrfToken = '<?php echo $csrf; ?>';</script>
    
    <script src="https://kit.fontawesome.com/b668f928a3.js" crossorigin="anonymous"></script>
    <script src="../../../public/js/perfil.js"></script>
</body>
</html>

    
    <?php include '../footer.php'; ?>