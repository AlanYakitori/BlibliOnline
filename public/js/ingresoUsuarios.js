
// CLASE: Usuario
class Usuario {
    #correo;
    #contrasena;
    #tipoUsuario;
    #preferencias;

    constructor(tipoUsuario) {
        this.#tipoUsuario = tipoUsuario;
    }

    // Getters
    get correo() { return this.#correo;}
    get contrasena() { return this.#contrasena; }
    get tipoUsuario() { return this.#tipoUsuario; }
    get preferencias() { return this.#preferencias; }

    // Setters
    set correo(correo) { this.#correo = correo; }
    set contrasena(contrasena) { this.#contrasena = contrasena; }
    set tipoUsuario(tipoUsuario) { this.#tipoUsuario = tipoUsuario; }
    set preferencias(preferencias) { this.#preferencias = preferencias; }
    
    // Método para inicializar el sistema de inicio de sesión
    inicializar() {
        const formulario = document.getElementById('formularioLogin');
        
        formulario.addEventListener('submit', (evento) => {
            evento.preventDefault();
            this.procesarIngreso();
        });
    }

    async procesarIngreso() {
        // Limpiar errores previos
        this.limpiarTodosLosErrores();

        // Obtener datos del formulario
        this.correo = document.getElementById('correoElectronico').value.trim();
        this.contrasena = document.getElementById('contrasena').value.trim();

        // Validar campos
        if (!this.validarCampos()) {
            this.mostrarMensaje('Por favor, complete todos los campos.', 'error');
            return;
        }

        // Preparar datos para enviar al servidor
        const datosUsuario = {
            correo: this.correo,
            contrasena: this.contrasena,
            tipoUsuario: this.tipoUsuario
        };

        // Mostrar mensaje de "ingresando..."
        this.mostrarMensaje('Ingresando...', 'info');

        // Enviar datos al servidor y esperar respuesta
        const respuestaServidor = await this.enviarAlServidor(datosUsuario);

        if (respuestaServidor.exito) {
            this.mostrarMensaje('Redirigiendo...', 'exito');
            
            // Guardar datos del usuario en localStorage para usar en el dashboard
            localStorage.setItem('usuarioActual', JSON.stringify({
                idUsuario: respuestaServidor.usuario.id_admin || 
                           respuestaServidor.usuario.id_docente || 
                           respuestaServidor.usuario.id_alumno,
                tipoUsuario: this.tipoUsuario
            }));
            
            if (this.tipoUsuario === 'administrador') {
                if (respuestaServidor.usuario.gusto === 1) {
                    setTimeout(() => {
                        window.location.href = 'dashboardAdministrador.html';
                    }, 2000);
                } else {
                    setTimeout(() => {
                        window.location.href = 'asignarPreferencias.html';
                    }, 2000);
                }    
            } else if (this.tipoUsuario === 'docente') {
                if (respuestaServidor.usuario.gusto === 1) {
                    setTimeout(() => {
                        window.location.href    = 'dashboardDocente.html';
                    }, 2000);
                } else {
                    setTimeout(() => {
                        window.location.href = 'asignarPreferencias.html';
                    }, 2000);
                }
            } else if (this.tipoUsuario === 'alumno') {
                if (respuestaServidor.usuario.gusto === 1) {
                    setTimeout(() => {
                        window.location.href = 'dashboardAlumno.html';
                    }, 2000);
                } else {
                    setTimeout(() => {
                        window.location.href = 'asignarPreferencias.html';
                    })
                }
            }
        } else {
            this.mostrarMensaje('Error: ' + respuestaServidor.mensaje, 'error');
        }
    }

    async enviarAlServidor(datosUsuario) {
        try {
            // Enviar datos al archivo PHP
            const respuesta = await fetch('procesarIngreso.php', {
                method: 'POST',                    // Método de envío
                headers: {                         // Configuración
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(datosUsuario) // Convertir datos a formato PHP
            });

            // Obtener respuesta del servidor PHP
            const resultado = await respuesta.json();
            return resultado;

        } catch (error) {
            console.error('Error al conectar con el servidor:', error);
            return {
                exito: false,
                mensaje: 'Error de conexión con el servidor'
            };
        }
    }

    

   

    // Método para validar los campos del formulario
    validarCampos() {
        return this.correo !== '' && this.contrasena !== '';
    }
    
}

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

        const formulario = document.getElementById('formularioRegistro');
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


    // Event listener del formulario
    const formulario = document.getElementById('formularioLogin');
    
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