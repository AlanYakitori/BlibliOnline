<?php
require_once __DIR__ . '/../../../config/session.php';

protegerPagina(['administrador', 'docente']); // Protege al docente
$csrf = obtenerCSRFToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BIBLIONLINE - Actualizar Docente</title>
    <link rel="stylesheet" href="../../../public/css/registro.css">
</head>
<body>
    <div class="contenedorRegistro">
        <div class="logoLibro">📚</div>
        
        <h1 class="tituloRegistro">BIBLIONLINE</h1>
        <p class="subTituloRegistro">Actualizar - Docente</p>
        <div class="indicadorTipoUsuario docente">
            <span class="iconoTipoUsuario">👨‍🏫</span>
            <span class="textoTipoUsuario">Docente</span>
        </div>
        
        <form class="formularioRegistro" id="formularioRegistro" disabled>
            <div class="filaInputs">
                <div class="grupoInput">
                    <label for="nombreCompleto" class="etiquetaInput">Nombre</label>
                    <input type="text" id="nombreCompleto" name="nombreCompleto" class="campoInput" placeholder="Ej: Carlos" required>
                </div>
                <div class="grupoInput">
                    <label for="apellidosCompletos" class="etiquetaInput">Apellidos</label>
                    <input type="text" id="apellidosCompletos" name="apellidosCompletos" class="campoInput" placeholder="Ej: García López" required>
                </div>
            </div>
            
            <div class="filaInputs">
                <div class="grupoInput">
                    <label for="telefonoContacto" class="etiquetaInput">Teléfono</label>
                    <input type="tel" id="telefonoContacto" name="telefonoContacto" class="campoInput" placeholder="Ej: 555-123-4567" required>
                </div>
                <div class="grupoInput">
                    <label for="especialidadDocente" class="etiquetaInput">Especialidad</label>
                    <input type="text" id="especialidadDocente" name="especialidadDocente" class="campoInput" placeholder="Ej: Desarrollo Web" required>
                </div>
            </div>
            
            <div class="filaInputs">
                <div class="grupoInput">
                    <label for="sexo" class="etiquetaInput">Sexo</label>
                    <select id="sexo" name="sexo" class="campoInput" required>
                        <option value="">Selecciona tu género</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                <div class="grupoInput">
                    <label for="fechaNacimiento" class="etiquetaInput">Fecha de Nacimiento</label>
                    <input type="date" id="fechaNacimiento" name="fechaNacimiento" class="campoInput" required>
                </div>
            </div>

            <div class="grupoInput">
                <label for="correoElectronico" class="etiquetaInput">Correo Electrónico</label>
                <input type="email" id="correoElectronico" name="correoElectronico" class="campoInput" placeholder="docente@biblionline.com" required>
            </div>
            
            <div class="filaInputs">
                <div class="grupoInput">
                    <label for="contrasena" class="etiquetaInput">Nueva Contraseña (Opcional)</label>
                    <input type="password" id="contrasena" name="contrasena" class="campoInput">
                </div>
                <div class="grupoInput">
                    <label for="confirmarContrasena" class="etiquetaInput">Confirmar Nueva Contraseña</label>
                    <input type="password" id="confirmarContrasena" name="confirmarContrasena" class="campoInput">
                </div>
            </div>
            
            <input type="submit" value="Actualizar Datos" class="botonRegistrarse">
            <div id="mensaje-feedback"></div>
        </form>
        
        <div class="enlacesAdicionales">
            <a href="consultarDocente.php" class="enlaceVolver">← Volver al panel</a>
        </div>
    </div>
    
    <script>window.csrfToken = '<?php echo $csrf; ?>';</script>
    <script src="../../../public/js/actualizarUsuarios.js"></script>
</body>
</html>