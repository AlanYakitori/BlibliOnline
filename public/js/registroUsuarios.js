document.addEventListener('DOMContentLoaded', function() {
    const url = window.location.pathname;
    let tipoUsuario = '';
    let aceptado = false;
    let gusto = false;
    let accion = 'registrar'; 
    
    // Determinar tipo de usuario según la URL
    if (url.includes('registroAdministrador')) {
        tipoUsuario = 'administrador';
    } else if (url.includes('registroDocente')) {
        tipoUsuario = 'docente';
    } else if (url.includes('registroAlumno')) {
        tipoUsuario = 'alumno';
        aceptado = true;
    }
    
    // Función para mostrar errores
    function mostrarError(campoId, mensaje) {
        const campo = document.getElementById(campoId) || document.querySelector(`[name="${campoId}"]`);
        if (campo) {
            campo.classList.add('campoError');
            
            let mensajeError = campo.parentNode.querySelector('.mensajeError');
            if (!mensajeError) {
                mensajeError = document.createElement('div');
                mensajeError.className = 'mensajeError';
                campo.parentNode.appendChild(mensajeError);
            }
            mensajeError.textContent = mensaje;
        }
    }
    
    // Función para limpiar errores
    function limpiarError(campoId) {
        const campo = document.getElementById(campoId) || document.querySelector(`[name="${campoId}"]`);
        if (campo) {
            campo.classList.remove('campoError');
            const mensajeError = campo.parentNode.querySelector('.mensajeError');
            if (mensajeError) {
                mensajeError.remove();
            }
        }
    }
    
    // Función para limpiar todos los errores
    function limpiarTodosLosErrores() {
        const camposConError = document.querySelectorAll('.campoError');
        camposConError.forEach(campo => campo.classList.remove('campoError'));
        
        const mensajesError = document.querySelectorAll('.mensajeError');
        mensajesError.forEach(mensaje => mensaje.remove());
        
        const mensajeGeneral = document.querySelector('.mensajeGeneral');
        if (mensajeGeneral) mensajeGeneral.remove();
    }
    
    // Función para mostrar mensaje general
    function mostrarMensaje(texto, tipo) {
        const mensajeAnterior = document.querySelector('.mensajeGeneral');
        if (mensajeAnterior) mensajeAnterior.remove();

        const mensaje = document.createElement('div');
        mensaje.className = `mensajeGeneral ${tipo}`;
        mensaje.textContent = texto;

        const formulario = document.getElementById('formularioRegistro');
        formulario.parentNode.insertBefore(mensaje, formulario);

        setTimeout(() => {
            if (mensaje.parentNode) mensaje.remove();
        }, 5000);
    }
    
    // Función para validar nombre
    function validarNombre(nombre) {
        if (!nombre || nombre.trim() === '') {
            mostrarError('nombreCompleto', 'El nombre es obligatorio');
            return false;
        }
        limpiarError('nombreCompleto');
        return true;
    }
    
    // Función para validar apellidos
    function validarApellidos(apellidos) {
        if (!apellidos || apellidos.trim() === '') {
            mostrarError('apellidosCompletos', 'Los apellidos son obligatorios');
            return false;
        }
        limpiarError('apellidosCompletos');
        return true;
    }
    
    // Función para validar teléfono (10 dígitos)
    function validarTelefono(telefono) {
        if (!telefono || telefono.trim() === '') {
            mostrarError('telefonoContacto', 'El teléfono es obligatorio');
            return false;
        }
        
        // Remover espacios, guiones y paréntesis para validar solo números
        const telefonoLimpio = telefono.replace(/[\s\-\(\)]/g, '');
        
        if (!/^\d{10}$/.test(telefonoLimpio)) {
            mostrarError('telefonoContacto', 'El teléfono debe tener exactamente 10 dígitos');
            return false;
        }
        
        limpiarError('telefonoContacto');
        return true;
    }
    
    // Función para validar dato
    function validarDato(dato) {
        if (!dato || dato.trim() === '') {
            mostrarError('datosCompletos', 'Este dato es obligatorio');
            return false;
        }
        limpiarError('datosCompletos');
        return true;
    }
    
    // Función para validar correo
    function validarCorreo(correo) {
        if (!correo || correo.trim() === '') {
            mostrarError('correoElectronico', 'El correo es obligatorio');
            return false;
        }
        
        const formatoCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!formatoCorreo.test(correo)) {
            mostrarError('correoElectronico', 'El formato del correo no es válido (ejemplo: prueba@gmail.com)');
            return false;
        }
        
        limpiarError('correoElectronico');
        return true;
    }
    
    // Función para validar contraseñas
    function validarContrasenas(contrasena, confirmarContrasena) {
        if (!contrasena || contrasena.trim() === '') {
            mostrarError('contrasena', 'La contraseña es obligatoria');
            return false;
        }
        
        if (!confirmarContrasena || confirmarContrasena.trim() === '') {
            mostrarError('confirmarContrasena', 'Debes confirmar la contraseña');
            return false;
        }
        
        if (contrasena !== confirmarContrasena) {
            mostrarError('confirmarContrasena', 'Las contraseñas no coinciden');
            return false;
        }
        
        limpiarError('contrasena');
        limpiarError('confirmarContrasena');
        return true;
    }
    
    // Función para validar género
    function validarGenero(genero) {
        if (!genero || genero.trim() === '') {
            mostrarError('sexo', 'Debes seleccionar tu género');
            return false;
        }
        
        limpiarError('sexo');
        return true;
    }
    
    // Función para validar fecha de nacimiento
    function validarFechaNacimiento(fechaNacimiento) {
        if (!fechaNacimiento || fechaNacimiento.trim() === '') {
            mostrarError('fechaNacimiento', 'La fecha de nacimiento es obligatoria');
            return false;
        }
        
        const fecha = new Date(fechaNacimiento);
        const fechaMinima = new Date('1920-01-01');
        const fechaMaxima = new Date('2015-01-01');
        
        if (fecha < fechaMinima || fecha > fechaMaxima) {
            mostrarError('fechaNacimiento', 'La fecha debe estar entre el 1 de enero de 1920 y el 1 de enero de 2015');
            return false;
        }
        
        limpiarError('fechaNacimiento');
        return true;
    }
    
    // Función principal para obtener y validar todos los datos
    function obtenerDatosValidados() {
        // Limpiar errores anteriores
        limpiarTodosLosErrores();
        
        // Obtener valores actuales del formulario
        const nombre = document.getElementById('nombreCompleto').value.trim();
        const apellidos = document.getElementById('apellidosCompletos').value.trim();
        const telefono = document.getElementById('telefonoContacto').value.trim();
        
        // Obtener dato específico según tipo de usuario
        let dato = '';
        if (tipoUsuario === 'administrador') {
            dato = document.getElementById('cargoAdministrativo').value.trim();
        } else if (tipoUsuario === 'docente') {
            dato = document.getElementById('areaEspecialidad').value.trim();
        } else if (tipoUsuario === 'alumno') {
            dato = document.getElementById('carreraEstudiante').value.trim();
        }
        
        const correo = document.getElementById('correoElectronico').value.trim();
        const contrasena = document.getElementById('contrasena').value;
        const confirmarContrasena = document.getElementById('confirmarContrasena').value;
        const genero = document.getElementById('sexo').value.trim();
        const fechaNacimiento = document.getElementById('fechaNacimiento').value;
        
        // Validar todos los campos
        let esValido = true;
        
        if (!validarNombre(nombre)) esValido = false;
        if (!validarApellidos(apellidos)) esValido = false;
        if (!validarTelefono(telefono)) esValido = false;
        if (!validarDato(dato, tipoUsuario)) esValido = false;
        if (!validarCorreo(correo)) esValido = false;
        if (!validarContrasenas(contrasena, confirmarContrasena)) esValido = false;
        if (!validarGenero(genero)) esValido = false;
        if (!validarFechaNacimiento(fechaNacimiento)) esValido = false;
        
        if (!esValido) {
            mostrarMensaje('Por favor corrige los errores en el formulario', 'error');
            return null;
        }
        
        // Retornar datos validados
        return {
            nombre: nombre,
            apellidos: apellidos,
            tipoUsuario: tipoUsuario,
            telefono: telefono,
            dato: dato,
            correo: correo,
            contrasena: contrasena,
            aceptado: aceptado,
            gusto: gusto,
            genero: genero,
            fechaNacimiento: fechaNacimiento,
            accion: accion
        };
    }
    
    // Función para enviar datos al servidor
    async function enviarDatos(datosValidados) {
        try {
            mostrarMensaje('Enviando datos...', 'info');
            
            const respuesta = await fetch('../../controllers/AuthController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(datosValidados)
            });
            
            const resultado = await respuesta.json();
            
            if (resultado.exito) {
                // Usar el mensaje del servidor
                mostrarMensaje(resultado.mensaje, 'exito');
                setTimeout(() => {
                    window.location.href = `login${tipoUsuario.charAt(0).toUpperCase() + tipoUsuario.slice(1)}.php`;
                }, 2000);
            } else {
                // Usar el mensaje específico del servidor
                mostrarMensaje(resultado.mensaje, 'error');
            }
            
        } catch (error) {
            console.error('Error al conectar con el servidor:', error);
            mostrarMensaje('Error de conexión con el servidor', 'error');
        }
    }
    
    // Event listener del formulario
    const formulario = document.getElementById('formularioRegistro');
    
    formulario.addEventListener('submit', async (evento) => {
        evento.preventDefault();
        
        // Obtener y validar datos
        const datosValidados = obtenerDatosValidados();
        
        if (datosValidados) {
            // Enviar datos al servidor
            await enviarDatos(datosValidados);
        }
    });
});

