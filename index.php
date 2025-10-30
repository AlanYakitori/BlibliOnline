<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BIBLIONLINE - Biblioteca Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="index.css">
</head>
<body>
 
    <div class="contenedorPrincipal">
        <!-- Logo de libro -->
        <div class="logoLibro">📚</div>
        
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
                <a href="loginAdministrador.html" class="botonTipoUsuario botonAdministrador">
                    <div class="iconoUsuario">👤</div>
                    <span class="textoBoton">Administrador</span>
                </a>
                
                <a href="loginDocente.html" class="botonTipoUsuario botonDocente">
                    <div class="iconoUsuario">👨‍🏫</div>
                    <span class="textoBoton">Docente</span>
                </a>
                
                <a href="loginAlumno.html" class="botonTipoUsuario botonAlumno">
                    <div class="iconoUsuario">🎓</div>
                    <span class="textoBoton">Alumno</span>
                </a>
                <?php 
                    if(isset($_GET['message'])){  
                ?> 
                <div class="alert alert-success" role="alert">
                   
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>