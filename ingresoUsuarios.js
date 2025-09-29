// ===================================
// CLASE: Usuario
// ===================================
class Usuario {
    #correo;
    #contrasena;
    #tipoUsuario;

    constructor(tipoUsuario) {
        this.#tipoUsuario = tipoUsuario;
    }

    // Getters
    get correo() { return this.#correo;}
    get contrasena() { return this.#contrasena; }
    get tipoUsuario() { return this.#tipoUsuario; }

    // Setters
    set correo(correo) { this.#correo = correo; }
    set contrasena(contrasena) { this.#contrasena = contrasena; }
    set tipoUsuario(tipoUsuario) { this.#tipoUsuario = tipoUsuario; }
    
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
                nombre: respuestaServidor.usuario.nombre,
                apellidos: respuestaServidor.usuario.apellidos,
                correo: respuestaServidor.usuario.correo,
                tipoUsuario: this.tipoUsuario,
                datosCompletos: respuestaServidor.usuario
            }));
            
            if (this.tipoUsuario === 'administrador') {
                setTimeout(() => {
                    window.location.href = 'dashboardAdministrador.html';
                }, 2000);
            } else if (this.tipoUsuario === 'docente') {
                setTimeout(() => {
                    window.location.href = 'dashboardDocente.html';
                }, 2000);
                // Mostrar en consola el nombre completo del docente
                console.log(`Docente: ${respuestaServidor.usuario.nombre} ${respuestaServidor.usuario.apellidos}`);
                console.log('Datos completos del docente:', respuestaServidor.usuario);
            } else if (this.tipoUsuario === 'alumno') {
                setTimeout(() => {
                    window.location.href = 'dashboardAlumno.html';
                }, 2000);
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

    // Método para mostrar mensajes al usuario
    mostrarMensaje(texto, tipo) {
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

    limpiarTodosLosErrores() {
        // Limpiar todos los campos con clase 'campoError'
        const camposConError = document.querySelectorAll('.campoError');
        camposConError.forEach(campo => {
            campo.classList.remove('campoError');
        });
        
        // Eliminar todos los mensajes de error
        const mensajesError = document.querySelectorAll('.mensajeError');
        mensajesError.forEach(mensaje => {
            mensaje.remove();
        });
        
        // Eliminar mensaje general si existe
        const mensajeGeneral = document.querySelector('.mensajeGeneral');
        if (mensajeGeneral) {
            mensajeGeneral.remove();
        }
    }

    // Método para validar los campos del formulario
    validarCampos() {
        return this.correo !== '' && this.contrasena !== '';
    }
    
}

// ===================================
// INICIALIZACIÓN AUTOMÁTICA
// ===================================
document.addEventListener('DOMContentLoaded', function() {
    // Detectar qué tipo de página de inicio es
    const url = window.location.pathname;
    let tipoUsuario;
    
    if (url.includes('loginAdministrador')) {
        tipoUsuario = 'administrador';
    } else if (url.includes('loginDocente')) {
        tipoUsuario = 'docente';
    } else if (url.includes('loginAlumno')) {
        tipoUsuario = 'alumno';
    }
    
    if (tipoUsuario) {
        try {
            const usuario = new Usuario(tipoUsuario);
            usuario.inicializar();
            console.log(`Sistema de inicio de sesión ${tipoUsuario} inicializado correctamente`);
        } catch (error) {
            console.error('Error al intentar acceder al inicio de sesión:', error);
        }
    }
});