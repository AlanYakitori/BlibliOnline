<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BIBLIONLINE - Registro Alumno</title>
    <link rel="stylesheet" href="../../../public/css/registro.css">
</head>
<body>
    <div class="contenedorRegistro">
        <div class="logoLibro">📚</div>
        
        <h1 class="tituloRegistro">BIBLIONLINE</h1>
        <p class="subTituloRegistro">Registro - Alumno</p>
        <div class="indicadorTipoUsuario alumno">
            <span class="iconoTipoUsuario">🎓</span>
            <span class="textoTipoUsuario">Alumno</span>
        </div>
        
        <form class="formularioRegistro" id="formularioRegistro">
            <div class="filaInputs">
                <div class="grupoInput">
                    <label for="nombreCompleto" class="etiquetaInput">Nombre</label>
                    <input 
                        type="text" 
                        id="nombreCompleto" 
                        name="nombreCompleto" 
                        class="campoInput" 
                        placeholder="Ej: Juan"
                        required
                    >
                </div>
                
                <div class="grupoInput">
                    <label for="apellidosCompletos" class="etiquetaInput">Apellidos</label>
                    <input 
                        type="text" 
                        id="apellidosCompletos" 
                        name="apellidosCompletos" 
                        class="campoInput" 
                        placeholder="Ej: Pérez Rodríguez"
                        required
                    >
                </div>
            </div>
            
            <div class="filaInputs">
                <div class="grupoInput">
                    <label for="telefonoContacto" class="etiquetaInput">Teléfono</label>
                    <input 
                        type="tel" 
                        id="telefonoContacto" 
                        name="telefonoContacto" 
                        class="campoInput" 
                        placeholder="Ej: 555-123-4567"
                        required
                    >
                </div>
                
                <div class="grupoInput">
                    <label for="carreraEstudiante" class="etiquetaInput">Carrera</label>
                    <select 
                        id="carreraEstudiante" 
                        name="carreraEstudiante" 
                        class="campoInput" 
                        required
                    >
                        <option value="">Selecciona una carrera</option>
                        <option value="Licenciatura en Administración">Licenciatura en Administración</option>
                        <option value="Ingeniería en Biotecnología">Ingeniería en Biotecnología</option>
                        <option value="Ingeniería Ambiental y Sustentabilidad">Ingeniería Ambiental y Sustentabilidad</option>
                        <option value="Ingeniería Industrial">Ingeniería Industrial</option>
                        <option value="Ingeniería en Tecnologías de la Información e Innovación Digital">Ingeniería en Tecnologías de la Información e Innovación Digital</option>
                        <option value="Ingeniería en Sistemas Electrónicos">Ingeniería en Sistemas Electrónicos</option>
                        <option value="Ingeniería Financiera">Ingeniería Financiera</option>
                        <option value="Maestría en Enseñanza de las Ciencias">Maestría en Enseñanza de las Ciencias</option>
                        <option value="Maestría en Tecnologías de la Información">Maestría en Tecnologías de la Información</option>
                        <option value="Maestría en Finanzas y Gestión">Maestría en Finanzas y Gestión</option>
                        <option value="Maestría en Ciencias en Biotecnología">Maestría en Ciencias en Biotecnología</option>
                        <option value="Doctorado en Ciencias en Biotecnología">Doctorado en Ciencias en Biotecnología</option>
                    </select>
                </div>
            </div>
            
            <div class="filaInputs">
                <div class="grupoInput">
                    <label for="sexo" class="etiquetaInput">Sexo</label>
                    <select 
                        id="sexo" 
                        name="sexo" 
                        class="campoInput" 
                        required
                    >
                        <option value="">Selecciona tu género</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                
                <div class="grupoInput">
                    <label for="fechaNacimiento" class="etiquetaInput">Fecha de Nacimiento</label>
                    <input 
                        type="date" 
                        id="fechaNacimiento" 
                        name="fechaNacimiento" 
                        class="campoInput" 
                        required
                    >
                </div>
            </div>
            
            <div class="grupoInput">
                <label for="correoElectronico" class="etiquetaInput">Correo Electrónico</label>
                <input 
                    type="email" 
                    id="correoElectronico" 
                    name="correoElectronico" 
                    class="campoInput" 
                    placeholder="alumno@biblionline.com"
                    required
                >
            </div>
            
            <div class="filaInputs">
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
            </div>
            
            <input type="submit" value="Registrarse" class="botonRegistrarse">
        </form>
        
        <div class="enlacesAdicionales">
            <p class="textoLogin">¿Ya tienes cuenta? <a href="loginAlumno.php" class="enlaceLogin">Inicia sesión aquí</a></p>
            <a href="../../../index.php" class="enlaceVolver">← Volver al inicio</a>
        </div>
    </div>
    
    <script src="../../../public/js/registroUsuarios.js"></script>
</body>
</html>