document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const correo = urlParams.get('id');
    const accion = 'cambiarContrasenia';
    
    // Métodos de manejo de errores visuales
    function mostrarError(campoId, mensaje) {
        const campo = document.getElementById(campoId);
        campo.classList.add('campoError');
        
        let mensajeError = campo.parentNode.querySelector('.mensajeError');
        if (!mensajeError) {
            mensajeError = document.createElement('div');
            mensajeError.className = 'mensajeError';
            campo.parentNode.appendChild(mensajeError);
        }
        mensajeError.textContent = mensaje;
    }

    function limpiarError(campoId) {
        const campo = document.getElementById(campoId);
        campo.classList.remove('campoError');
        const mensajeError = campo.parentNode.querySelector('.mensajeError');
        if (mensajeError) mensajeError.remove();
    }

    function limpiarTodosLosErrores() {
        document.querySelectorAll('.campoError').forEach(campo => campo.classList.remove('campoError'));
        document.querySelectorAll('.mensajeError').forEach(msg => msg.remove());
        const mensajeGeneral = document.querySelector('.mensajeGeneral');
        if (mensajeGeneral) mensajeGeneral.remove();
    }

    function mostrarMensaje(texto, tipo) {
        const anterior = document.querySelector('.mensajeGeneral');
        if (anterior) anterior.remove();

        const mensaje = document.createElement('div');
        mensaje.className = `mensajeGeneral ${tipo}`;
        mensaje.textContent = texto;

        const formulario = document.getElementById('formularioContrasenia');
        formulario.parentNode.insertBefore(mensaje, formulario);

        setTimeout(() => mensaje.remove(), 5000);
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

    function obtenerDatosValidados() {
        limpiarTodosLosErrores();

        const contrasena = document.getElementById('contrasena').value.trim();
        const confirmarContrasena = document.getElementById('confirmarContrasena').value.trim();
    
        let esValido = true;

        if (!validarContrasenas(contrasena, confirmarContrasena)) esValido = false;
        
        if (!esValido) {
            mostrarMensaje('Por favor corrige los errores en el formulario', 'error');
            return null;
        }

        return {
            contrasenia: contrasena,
            correo : correo,
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
                    window.location.href = `../../../index.php`;
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

    const formulario = document.getElementById('formularioContrasenia');
    
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
