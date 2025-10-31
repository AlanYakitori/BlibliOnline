// CLASE: Preferencias
class Preferencias {
    #usuarioId;
    #tipoUsuario;

    constructor(usuarioId, tipoUsuario) {
        this.#usuarioId = usuarioId;
        this.#tipoUsuario = tipoUsuario;
    }
    
    // Getters
    get usuarioId() { return this.#usuarioId; }
    get tipoUsuario() { return this.#tipoUsuario; }

    // Setters
    set usuarioId(usuarioId) { this.#usuarioId = usuarioId; }
    set tipoUsuario(tipoUsuario) { this.#tipoUsuario = tipoUsuario; }
    
    // Método para inicializar el sistema de inicio de sesión
    inicializar() {
        const formulario = document.getElementById('formularioPreferencias');
        
        formulario.addEventListener('submit', (evento) => {
            evento.preventDefault();
            this.procesarCategorias();
        });
    }

    async procesarCategorias() {
        // Limpiar errores previos
        this.limpiarTodosLosErrores();

        // Obtener todas las categorías seleccionadas
        const categoriasSeleccionadas = document.querySelectorAll('input[name="categorias"]:checked');
        
        // Validar que se haya seleccionado al menos una categoría
        if (categoriasSeleccionadas.length === 0) {
            this.mostrarMensaje('Por favor, selecciona al menos una categoría de tu interés.', 'error');
            return;
        }
        
        // Obtener solo los números de las categorías seleccionadas
        const numerosCategorias = [];
        categoriasSeleccionadas.forEach(function(checkbox) {
            numerosCategorias.push(parseInt(checkbox.value));
        });
 
        // Preparar datos para enviar al servidor
        const datosUsuario = {
            idUsuario: this.usuarioId,
            tipoUsuario: this.tipoUsuario,
            categoriasSeleccionadas: numerosCategorias
        };

        // Mostrar mensaje de "ingresando..."
        this.mostrarMensaje('Ingresando...', 'info');

        // Enviar datos al servidor y esperar respuesta
        const respuestaServidor = await this.enviarAlServidor(datosUsuario);

        if (respuestaServidor.exito) {
            this.mostrarMensaje('Redirigiendo...', 'exito');
            
            // Guardar datos del usuario en localStorage para usar en el dashboard
            localStorage.setItem('usuarioActual', JSON.stringify({
                idUsuario: this.usuarioId,
                tipoUsuario: this.tipoUsuario
            }));
            
            if (this.tipoUsuario === 'administrador') {
                setTimeout(() => {
                        window.location.href = 'dashboardAdministrador.html';
                }, 2000);  
            } else if (this.tipoUsuario === 'docente') {
                setTimeout(() => {
                        window.location.href    = 'dashboardDocente.html';
                }, 2000);
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
            const respuesta = await fetch('procesarPreferencias.php', {
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

        const formulario = document.getElementById('formularioPreferencias');
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
}

// INICIALIZACIÓN AUTOMÁTICA
document.addEventListener('DOMContentLoaded', function() {
    // Obtener datos del usuario desde localStorage
    const datosUsuarioGuardado = localStorage.getItem('usuarioActual');

    if (!datosUsuarioGuardado) {
        mostrarMensaje('Error: No se encontraron datos del usuario', 'error');
        setTimeout(() => {
            window.location.href = 'index.php';
        }, 2000);
        return;
    }

    const datosUsuario = JSON.parse(datosUsuarioGuardado);
    const usuarioId = datosUsuario.idUsuario;
    const tipoUsuario = datosUsuario.tipoUsuario;
    
    const preferencias = new Preferencias(usuarioId, tipoUsuario);
    preferencias.inicializar();
});