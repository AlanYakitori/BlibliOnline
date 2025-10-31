// CLASE PADRE: Usuario
class Contrasenia {
    // Atributos privados
    #contrasena;
    #confirmarContrasena;



    constructor() {
        this.#contrasena = '';
        this.#confirmarContrasena = '';
    }

    // Getters
    get contrasena() { return this.#contrasena; }
    get confirmarContrasena() { return this.#confirmarContrasena; }

    // Setters
    set contrasena(valor) { this.#contrasena = valor; }
    set confirmarContrasena(valor) { this.#confirmarContrasena = valor; }

    // Capturar datos básicos del formulario
    capturarDatosBasicos() {
        this.#contrasena = document.getElementById('contrasena').value.trim();
        this.#confirmarContrasena = document.getElementById('confirmarContrasena').value.trim();
    }

    // Validaciones
    validarContrasena() {
        if (!this.#contrasena) {
            this.mostrarError('contrasena', 'La contraseña es obligatoria');
            return false;
        }
        if (this.#contrasena.length < 6) {
            this.mostrarError('contrasena', 'La contraseña debe tener al menos 6 caracteres');
            return false;
        }
        if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(this.#contrasena)) {
            this.mostrarError('contrasena', 'Debe contener mayúsculas, minúsculas y números');
            return false;
        }
        this.limpiarError('contrasena');
        return true;
    }

    validarConfirmarContrasena() {
        if (!this.#confirmarContrasena) {
            this.mostrarError('confirmarContrasena', 'Confirma tu contraseña');
            return false;
        }
        if (this.#contrasena !== this.#confirmarContrasena) {
            this.mostrarError('confirmarContrasena', 'Las contraseñas no coinciden');
            return false;
        }
        this.limpiarError('confirmarContrasena');
        return true;
    }

    validarTodos() {
        let esValido = true;
        if (!this.validarContrasena()) esValido = false;
        if (!this.validarConfirmarContrasena()) esValido = false;
        return esValido;
    }

    // Métodos de manejo de errores visuales
    mostrarError(campoId, mensaje) {
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

    limpiarError(campoId) {
        const campo = document.getElementById(campoId);
        campo.classList.remove('campoError');
        const mensajeError = campo.parentNode.querySelector('.mensajeError');
        if (mensajeError) mensajeError.remove();
    }

    limpiarTodosLosErrores() {
        document.querySelectorAll('.campoError').forEach(campo => campo.classList.remove('campoError'));
        document.querySelectorAll('.mensajeError').forEach(msg => msg.remove());
        const mensajeGeneral = document.querySelector('.mensajeGeneral');
        if (mensajeGeneral) mensajeGeneral.remove();
    }

    mostrarMensaje(texto, tipo) {
        const anterior = document.querySelector('.mensajeGeneral');
        if (anterior) anterior.remove();

        const mensaje = document.createElement('div');
        mensaje.className = `mensajeGeneral ${tipo}`;
        mensaje.textContent = texto;

        const formulario = document.getElementById('formularioContrasenia');
        formulario.parentNode.insertBefore(mensaje, formulario);

        setTimeout(() => mensaje.remove(), 5000);
    }

    // Obtener datos como objeto
    obtenerDatos() {
        return {
            contrasena: this.#contrasena
        };
    }

    // Enviar datos al servidor PHP
    async enviarAlServidor(datosUsuario, correo, tipoUsuario) {
        try {

            const datosCompletos = {
            ...datosUsuario,
            correo: correo,
            tipoUsuario: tipoUsuario
            };

            const respuesta = await fetch('procesarContrasenia.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datosCompletos)
            });
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

    // Procesar el registro
    async procesarRegistro(correo, tipoUsuario) {
        this.limpiarTodosLosErrores();
        this.capturarDatosBasicos();

        if (!this.validarTodos()) {
            this.mostrarMensaje('Por favor corrige los errores en el formulario', 'error');
            return false;
        }

        const datosValidados = this.obtenerDatos();

        this.mostrarMensaje('Guardando contraseña...', 'info');

        const respuestaServidor = await this.enviarAlServidor(datosValidados, correo, tipoUsuario);

        if (respuestaServidor.exito) {
            this.mostrarMensaje('¡Contraseña guardada correctamente!', 'exito');
            setTimeout(() => {
                window.location.href = 'index.php';
            }, 2000);
        } else {
            this.mostrarMensaje('Error: ' + respuestaServidor.mensaje, 'error');
        }

        return datosValidados;
    }

    inicializar(correo, tipoUsuario) {
        const formulario = document.getElementById('formularioContrasenia');
        formulario.addEventListener('submit', (evento) => {
            evento.preventDefault();
            this.procesarRegistro(correo, tipoUsuario);
        });
    }
}

// Inicializar al cargar
document.addEventListener('DOMContentLoaded', () => {

    const url = window.location.href;
    let tipoUsuario = '';

    if (url.includes('cambiarContraseniaAdministrador')) {
        tipoUsuario = 'administrador';
    } else if (url.includes('cambiarContraseniaDocente')) {
        tipoUsuario = 'docente';
    } else if (url.includes('cambiarContraseniaAlumno')) {
        tipoUsuario = 'alumno';
    }

    const urlParams = new URLSearchParams(window.location.search);

    // Obtener el valor del parámetro "id"
    const correo = urlParams.get('id');

    const gestorContrasenia = new Contrasenia();
    gestorContrasenia.inicializar(correo, tipoUsuario);
});
