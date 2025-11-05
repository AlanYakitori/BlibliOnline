<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BIBLIONLINE - Biblioteca Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="public/css/index.css">
</head>
<body>
<div class="boxBienvenida">
    <div class="contenedorPrincipal">
        <!-- Logo de libro -->
        
        
        <!-- Título principal -->
        <h1 class="tituloPrincipal">BIBLIONLINE</h1>
        <p class="subTitulo">Tu Biblioteca Digital</p>
        
        <!-- Texto de bienvenida -->
        <div class="textoBienvenida">
            <p>¡Bienvenido a BIBLIONLINE!</p>
            <p>Descubre miles de libros digitales, artículos académicos y recursos educativos al alcance de un clic. Nuestra plataforma te ofrece acceso ilimitado a una vasta colección literaria desde la comodidad de tu hogar.</p>
        </div>
        
        <!-- Selección de tipo de usuario -->
        <div class="seccionUsuarios">
            <h2 class="tituloUsuarios">Selecciona tu inicio de sesión</h2>
            <div class="contenedorBotones">
                <a href="app/views/auth/loginAdministrador.php" class="botonTipoUsuario botonAdministrador">
                    <span class="textoBoton">Administrador</span>
                </a>
                
                <a href="app/views/auth/loginDocente.php" class="botonTipoUsuario botonDocente">
                    <span class="textoBoton">Docente</span>
                </a>
                
                <a href="app/views/auth/loginAlumno.php" class="botonTipoUsuario botonAlumno">
                    <span class="textoBoton">Alumno</span>
                </a>
                <?php 
                    if(isset($_GET['message'])){  
                ?> 
                <div class="alert alert-primary" role="alert">
                   
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
                <?php 
                    }
                ?>   
            </div>
            </div>
            
        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
<footer class="pie">
    <div class="grupo1">
        <div class="box">
            <a href="">Ayuda</a>
        </div>
        <div class="box">
            <a href="#" class="">Quienes somos?</a>
        </div>
    </div>
    <div class="grupo2">
        <small>&copy; 2024 <b>BIBLIONLINE</b> - Todos los Derechos Reservados.</small>
    </div>
</footer>
</html>