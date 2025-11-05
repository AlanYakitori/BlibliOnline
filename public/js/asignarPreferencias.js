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
    const usuarioId = datosUsuario.id;
    const tipoUsuario = datosUsuario.tipoUsuario;
    let accion = 'agregarPreferencias';

    function mostrarMensaje(texto, tipo) {
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

    function limpiarTodosLosErrores() {
        const camposConError = document.querySelectorAll('.campoError');
        camposConError.forEach(campo => {
            campo.classList.remove('campoError');
        });
        
        const mensajesError = document.querySelectorAll('.mensajeError');
        mensajesError.forEach(mensaje => {
            mensaje.remove();
        });
        
        const mensajeGeneral = document.querySelector('.mensajeGeneral');
        if (mensajeGeneral) {
            mensajeGeneral.remove();
        }
    }   

    async function procesarCategorias() {
        // Limpiar errores previos
        limpiarTodosLosErrores();

        // Obtener todas las categorías seleccionadas
        const categoriasSeleccionadas = document.querySelectorAll('input[name="categorias"]:checked');
        
        // Validar que se haya seleccionado al menos una categoría
        if (categoriasSeleccionadas.length === 0) {
            mostrarMensaje('Por favor, selecciona al menos una categoría de tu interés.', 'error');
            return;
        }
        
        // Obtener solo los números de las categorías seleccionadas
        const numerosCategorias = [];
        categoriasSeleccionadas.forEach(function(checkbox) {
            numerosCategorias.push(parseInt(checkbox.value));
        });
 
        // Preparar datos para enviar al servidor
        const datosUsuario = {
            usuarioId: usuarioId,
            tipoUsuario: tipoUsuario,
            categoriasSeleccionadas: numerosCategorias,
            accion: accion
        };

        // Mostrar mensaje de "ingresando..."
        mostrarMensaje('Ingresando...', 'info');

        // Enviar datos al servidor y esperar respuesta
        const respuestaServidor = await enviarAlServidor(datosUsuario);

        if (respuestaServidor.exito) {
            mostrarMensaje('Preferencias guardadas exitosamente. Redirigiendo...', 'exito');
            
            // Actualizar los datos del usuario en localStorage (marcar gusto = 1)
            const datosActualizados = JSON.parse(localStorage.getItem('usuarioActual'));
            datosActualizados.gusto = 1;
            localStorage.setItem('usuarioActual', JSON.stringify(datosActualizados));
            
            // Redirigir según el tipo de usuario a sus dashboards correspondientes
            setTimeout(() => {
                if (tipoUsuario === 'administrador') {
                    window.location.href = '../dashboard/administrador.php';
                } else if (tipoUsuario === 'docente') {
                    window.location.href = '../dashboard/docente.php';
                } else if (tipoUsuario === 'alumno') {
                    window.location.href = '../dashboard/alumno.php';
                }
            }, 2000);
        } else {
            mostrarMensaje('Error: ' + respuestaServidor.mensaje, 'error');
        }
    }

    async function enviarAlServidor(datosUsuario) {
        try {
            // Enviar datos al controlador PHP correcto
            const respuesta = await fetch('../../controllers/PreferencesController.php', {
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

    const formulario = document.getElementById('formularioPreferencias');
        
    formulario.addEventListener('submit', (evento) => {
        evento.preventDefault();
        procesarCategorias();
    });
});