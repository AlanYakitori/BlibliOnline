document.addEventListener('DOMContentLoaded', function() {
    const url = window.location.pathname;
    let tipoUsuario = '';
    let accion = 'ingresar';

    // Determinar el tipo de usuario según la URL
    if (url.includes('loginAdministrador')) {
        tipoUsuario = 'administrador';
    } else if (url.includes('loginDocente')) {
        tipoUsuario = 'docente';
    } else if (url.includes('loginAlumno')) {
        tipoUsuario = 'alumno';
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

        const formulario = document.getElementById('formularioLogin');
        formulario.parentNode.insertBefore(mensaje, formulario);

        setTimeout(() => {
            if (mensaje.parentNode) mensaje.remove();
        }, 5000);
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

    // Función para validar contraseña
    function validarContrasenia(nombre) {
        if (!nombre || nombre.trim() === '') {
            mostrarError('contraseniaCompleto', 'La contraseña es obligatorio');
            return false;
        }
        limpiarError('contraseniaCompleto');
        return true;
    }

    // Función para obtener y validar datos del formulario
    function obtenerDatosValidados() {
        limpiarTodosLosErrores();
        
        const correo = document.getElementById('correoElectronico').value.trim();
        const contrasena = document.getElementById('contrasena').value.trim();  

        let esValido = true;

        if (!validarCorreo(correo)) esValido = false;
        if (!validarContrasenia(contrasena)) esValido = false;

        if (!esValido) {
            mostrarMensaje('Por favor, corrija los errores en el formulario.', 'error');
            return null;
        }

        return {
            correo: correo,
            contrasena: contrasena,
            tipoUsuario: tipoUsuario,
            accion: accion
        };
    }

    // Función para enviar datos al servidor
    async function enviarDatos(datosValidados) {
        try {
            mostrarMensaje('Iniciando sesión...', 'info');
            
            const respuesta = await fetch('../../controllers/AuthController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(datosValidados)
            });
            
            const resultado = await respuesta.json();
            
            if (resultado.exito) {
                mostrarMensaje('Inicio de sesión exitoso. Redirigiendo...', 'exito');
                
                // Guardar datos del usuario en localStorage
                localStorage.setItem('usuarioActual', JSON.stringify({
                    id: resultado.usuario.id,
                    nombre: resultado.usuario.nombre,
                    apellidos: resultado.usuario.apellidos,
                    tipoUsuario: resultado.usuario.tipoUsuario,
                    telefono: resultado.usuario.telefono,
                    dato: resultado.usuario.dato,
                    correo: resultado.usuario.correo,
                    aceptado: resultado.usuario.aceptado,
                    gusto: resultado.usuario.gusto,
                    genero: resultado.usuario.genero,
                    fechaNacimiento: resultado.usuario.fechaNacimiento
                }));
                
                // Redirigir según el tipo de usuario y gusto
                setTimeout(() => {
                    if (tipoUsuario === 'administrador') {
                        if (resultado.usuario.gusto == 1) {
                            window.location.href = '../dashboard/administrador.php';
                        } else {
                            window.location.href = 'asignarPreferencias.php';
                        }    
                    } else if (tipoUsuario === 'docente') {
                        if (resultado.usuario.gusto == 1) {
                            window.location.href = '../dashboard/docente.php';
                        } else {
                            window.location.href = 'asignarPreferencias.php';
                        }
                    } else if (tipoUsuario === 'alumno') {
                        if (resultado.usuario.gusto == 1) {
                            window.location.href = '../dashboard/alumno.php';
                        } else {
                            window.location.href = 'asignarPreferencias.php';
                        }
                    }
                }, 2000);
            } else {
                mostrarMensaje(resultado.mensaje, 'error');
            }
            
        } catch (error) {
            console.error('Error al conectar con el servidor:', error);
            mostrarMensaje('Error de conexión con el servidor', 'error');
        }
    }

    // Event listener del formulario
    const formulario = document.getElementById('formularioLogin');
    
    formulario.addEventListener('submit', async (evento) => {
        evento.preventDefault();
        
        // Obtener y validar datos
        const datosValidados = obtenerDatosValidados();
        
        if (datosValidados) {
            // Enviar datos al servidor y esperar respuesta
           await enviarDatos(datosValidados); 
        }
    });
});