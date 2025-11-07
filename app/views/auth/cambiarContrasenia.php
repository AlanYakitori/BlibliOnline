<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BIBLIONLINE - Iniciar Sesión</title>
    <link rel="stylesheet" href="../../../public/css/login.css">
</head>
<body>
<div class="cajota">
    <div class="contenedorLogin">
        <div class="logoLibro">📚</div>
        
        <h1 class="tituloLogin">BIBLIONLINE</h1>
        <p class="subTituloLogin">Recuperación de contraseña</p>
        
        <form class="formularioLogin" id="formularioContrasenia">
            
            <div class="grupoInput">
                    <label for="contrasena" class="etiquetaInput">Contraseña</label>
                    <input 
                        type="password" 
                        id="contrasena" 
                        name="contrasena" 
                        class="campoInput" 
                        placeholder="••••••••"
                        required
                    >
                </div>
                
                <div class="grupoInput">
                    <label for="confirmarContrasena" class="etiquetaInput">Confirmar Contraseña</label>
                    <input 
                        type="password" 
                        id="confirmarContrasena" 
                        name="confirmarContrasena" 
                        class="campoInput" 
                        placeholder="••••••••"
                        required
                    >
                </div>
            
            <input type="submit" value="Actualizar" class="botonIngresar">
        </form>
    </div>
</div>
    <script src="../../../public/js/actualizarContrasenia.js"></script>
    
</body>
</html>