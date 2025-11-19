<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BIBLIONLINE - Biblioteca Digital</title>
    
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.7.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/index.css">
    <link rel="stylesheet" href="public/css/footer.css">
</head>
<body>
 
    <div class="contenedorPrincipal">
        
        <h1 class="tituloPrincipal">BIBLIONLINE</h1>
        
        <h2 class="main-slogan">Tu acceso ilimitado al saber y la cultura.</h2> 
        
        <p class="subTitulo">Tu Biblioteca Digital</p>
        
        <div class="textoBienvenida">
            <p>Descubre miles de libros digitales, artículos académicos y recursos educativos al alcance de un clic. Nuestra plataforma te ofrece acceso ilimitado a una vasta colección literaria desde la comodidad de tu hogar.</p>
        </div>
        
        <div class="seccionUsuarios">
            <h2 class="tituloUsuarios">Selecciona tu inicio de sesión</h2>
            <div class="contenedorBotones">
                
                <a href="app/views/auth/loginAdministrador.php" class="botonTipoUsuario botonAdministrador">
                    <i class="ri-shield-user-line"></i> 
                    <span class="textoBoton">Administrador</span>
                </a>
                
                <a href="app/views/auth/loginDocente.php" class="botonTipoUsuario botonDocente">
                    <i class="ri-user-star-line"></i> 
                    <span class="textoBoton">Docente</span>
                </a>
                
                <a href="app/views/auth/loginAlumno.php" class="botonTipoUsuario botonAlumno">
                    <i class='bx bx-user'></i> 
                    <span class="textoBoton">Alumno</span>
                </a>
                
                <?php 
                    if(isset($_GET['message'])){ 
                ?> 
                <div class="custom-alert type-success" role="alert"> 
                    <?php
                        switch ($_GET['message']){
                            case 'ok':
                                echo 'Correo enviado con exito, por favor, revise su badeja de mensajes';
                                break;
                            default:
                                echo 'Algo salio mal, no se porque la notificacion es verde!';
                                break;
                        }
                    ?> 
                </div> 
                <?php 
                    }
                ?> 
            </div> 
        </div> 
    </div>
    
    <?php include 'app/views/footer.php'; ?>
</body>
</html>