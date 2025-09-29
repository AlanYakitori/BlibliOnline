// ===================================
// CLASE PADRE: Usuario
// ===================================
class Usuario {
    // Atributos privados
    #nombre;
    #apellidos;
    #telefono;
    #correo;
    #contrasena;
    #confirmarContrasena;
    #tipoUsuario;

    constructor(tipoUsuario) {
        this.#tipoUsuario = tipoUsuario;
    }

    // Getters
    get nombre() { return this.#nombre; }
    get apellidos() { return this.#apellidos; }
    get telefono() { return this.#telefono; }
    get correo() { return this.#correo; }
    get contrasena() { return this.#contrasena; }
    get confirmarContrasena() { return this.#confirmarContrasena; }
    get tipoUsuario() { return this.#tipoUsuario; }

    // Setters
    set nombre(valor) { this.#nombre = valor?.trim(); }
    set apellidos(valor) { this.#apellidos = valor?.trim(); }
    set telefono(valor) { this.#telefono = valor?.trim(); }
    set correo(valor) { this.#correo = valor?.trim(); }
    set contrasena(valor) { this.#contrasena = valor; }
    set confirmarContrasena(valor) { this.#confirmarContrasena = valor; }

    // Capturar datos básicos del formulario
    capturarDatosBasicos() {
        this.#nombre = document.getElementById('nombreCompleto').value.trim();
        this.#apellidos = document.getElementById('apellidosCompletos').value.trim();
        this.#telefono = document.getElementById('telefonoContacto').value.trim();
        this.#correo = document.getElementById('correoElectronico').value.trim();
        this.#contrasena = document.getElementById('contrasena').value;
        this.#confirmarContrasena = document.getElementById('confirmarContrasena').value;
    }

    // Validaciones individuales
    validarNombre() {
        if (!this.#nombre || this.#nombre === '') {
            this.mostrarError('nombreCompleto', 'El nombre es obligatorio');
            return false;
        }
        if (this.#nombre.length < 2) {
            this.mostrarError('nombreCompleto', 'El nombre debe tener al menos 2 caracteres');
            return false;
        }
        this.limpiarError('nombreCompleto');
        return true;
    }

    validarApellidos() {
        if (!this.#apellidos || this.#apellidos === '') {
            this.mostrarError('apellidosCompletos', 'Los apellidos son obligatorios');
            return false;
        }
        if (this.#apellidos.length < 2) {
            this.mostrarError('apellidosCompletos', 'Los apellidos deben tener al menos 2 caracteres');
            return false;
        }
        this.limpiarError('apellidosCompletos');
        return true;
    }

    validarTelefono() {
        if (!this.#telefono || this.#telefono === '') {
            this.mostrarError('telefonoContacto', 'El teléfono es obligatorio');
            return false;
        }
        const formatoValidoTelefono = /^[\d\s\-\+\(\)]{10,15}$/;
        if (!formatoValidoTelefono.test(this.#telefono)) {
            this.mostrarError('telefonoContacto', 'Formato de teléfono no válido (10-15 dígitos)');
            return false;
        }
        this.limpiarError('telefonoContacto');
        return true;
    }

    validarCorreo() {
        if (!this.#correo || this.#correo === '') {
            this.mostrarError('correoElectronico', 'El correo es obligatorio');
            return false;
        }
        const formatoValidoCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!formatoValidoCorreo.test(this.#correo)) {
            this.mostrarError('correoElectronico', 'El formato del correo no es válido');
            return false;
        }
        this.limpiarError('correoElectronico');
        return true;
    }

    validarContrasena() {
        if (!this.#contrasena || this.#contrasena === '') {
            this.mostrarError('contrasena', 'La contraseña es obligatoria');
            return false;
        }
        if (this.#contrasena.length < 6) {
            this.mostrarError('contrasena', 'La contraseña debe tener al menos 6 caracteres');
            return false;
        }
        if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(this.#contrasena)) {
            this.mostrarError('contrasena', 'La contraseña debe tener mayúsculas, minúsculas y números');
            return false;
        }
        this.limpiarError('contrasena');
        return true;
    }

    validarConfirmarContrasena() {
        if (!this.#confirmarContrasena || this.#confirmarContrasena === '') {
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

    // Validación completa (base)
    validarTodos() {
        let esValido = true;
        
        if (!this.validarNombre()) esValido = false;
        if (!this.validarApellidos()) esValido = false;
        if (!this.validarTelefono()) esValido = false;
        if (!this.validarCorreo()) esValido = false;
        if (!this.validarContrasena()) esValido = false;
        if (!this.validarConfirmarContrasena()) esValido = false;
        
        return esValido;
    }

    // Métodos para manejo de errores
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
        if (mensajeError) {
            mensajeError.remove();
        }
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

    mostrarMensaje(texto, tipo) {
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

    // Obtener datos como objeto
    obtenerDatos() {
        return {
            nombre: this.#nombre,
            apellidos: this.#apellidos,
            telefono: this.#telefono,
            correo: this.#correo,
            contrasena: this.#contrasena,
            tipoUsuario: this.#tipoUsuario
        };
    }

    // Función para enviar datos al servidor PHP
    async enviarAlServidor(datosUsuario) {
        try {
            // Enviar datos al archivo PHP
            const respuesta = await fetch('procesarRegistro.php', {
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
}

// ===================================
// CLASE HIJA: Administrador
// ===================================
class Administrador extends Usuario {
    #cargo;

    constructor() {
        super('administrador');
    }

    get cargo() { return this.#cargo; }
    set cargo(valor) { this.#cargo = valor?.trim(); }

    capturarDatos() {
        super.capturarDatosBasicos();
        this.#cargo = document.getElementById('cargoAdministrativo').value.trim();
    }

    validarCargo() {
        if (!this.#cargo || this.#cargo === '') {
            this.mostrarError('cargoAdministrativo', 'El cargo es obligatorio');
            return false;
        }
        if (this.#cargo.length < 3) {
            this.mostrarError('cargoAdministrativo', 'El cargo debe tener al menos 3 caracteres');
            return false;
        }
        this.limpiarError('cargoAdministrativo');
        return true;
    }

    validarTodos() {
        let esValido = super.validarTodos();
        if (!this.validarCargo()) esValido = false;
        return esValido;
    }

    obtenerDatos() {
        const datosBasicos = super.obtenerDatos();
        return {
            ...datosBasicos,
            cargo: this.#cargo
        };
    }

    async procesarRegistro() {
        // Limpiar todos los errores anteriores
        this.limpiarTodosLosErrores();
        
        this.capturarDatos();
        
        if (!this.validarTodos()) {
            this.mostrarMensaje('Por favor corrige los errores en el formulario', 'error');
            return false;
        }

        const datosValidados = this.obtenerDatos();
        console.log('Datos del administrador validados:', datosValidados);
        
        // Mostrar mensaje de "enviando..."
        this.mostrarMensaje('Guardando administrador...', 'info');
        
        // Enviar datos al servidor PHP
        const respuestaServidor = await this.enviarAlServidor(datosValidados);
        
        if (respuestaServidor.exito) {
            this.mostrarMensaje('¡Administrador registrado correctamente!', 'exito');
            // Opcional: limpiar formulario o redirigir
            setTimeout(() => {
                window.location.href = 'loginAdministrador.html';
            }, 2000);
        } else {
            this.mostrarMensaje('Error: ' + respuestaServidor.mensaje, 'error');
        }
        
        return datosValidados;
    }

    inicializar() {
        const formulario = document.getElementById('formularioRegistro');
        
        formulario.addEventListener('submit', (evento) => {
            evento.preventDefault();
            this.procesarRegistro();
        });
    }
}

// ===================================
// CLASE HIJA: Docente
// ===================================
class Docente extends Usuario {
    #especialidad;

    constructor() {
        super('docente');
    }

    get especialidad() { return this.#especialidad; }
    set especialidad(valor) { this.#especialidad = valor?.trim(); }

    capturarDatos() {
        super.capturarDatosBasicos();
        this.#especialidad = document.getElementById('areaEspecialidad').value.trim();
    }

    validarEspecialidad() {
        if (!this.#especialidad || this.#especialidad === '') {
            this.mostrarError('areaEspecialidad', 'La especialidad es obligatoria');
            return false;
        }
        if (this.#especialidad.length < 3) {
            this.mostrarError('areaEspecialidad', 'La especialidad debe tener al menos 3 caracteres');
            return false;
        }
        this.limpiarError('areaEspecialidad');
        return true;
    }

    validarTodos() {
        let esValido = super.validarTodos();
        if (!this.validarEspecialidad()) esValido = false;
        return esValido;
    }

    obtenerDatos() {
        const datosBasicos = super.obtenerDatos();
        return {
            ...datosBasicos,
            especialidad: this.#especialidad
        };
    }

    async procesarRegistro() {
        // Limpiar todos los errores anteriores
        this.limpiarTodosLosErrores();
        
        this.capturarDatos();
        
        if (!this.validarTodos()) {
            this.mostrarMensaje('Por favor corrige los errores en el formulario', 'error');
            return false;
        }

        const datosValidados = this.obtenerDatos();
        console.log('Datos del docente validados:', datosValidados);
        
        // Mostrar mensaje de "enviando..."
        this.mostrarMensaje('Guardando docente...', 'info');
        
        // Enviar datos al servidor PHP
        const respuestaServidor = await this.enviarAlServidor(datosValidados);
        
        if (respuestaServidor.exito) {
            this.mostrarMensaje('¡Docente registrado correctamente!', 'exito');
            // Opcional: limpiar formulario o redirigir
            setTimeout(() => {
                window.location.href = 'loginDocente.html';
            }, 2000);
        } else {
            this.mostrarMensaje('Error: ' + respuestaServidor.mensaje, 'error');
        }
        
        return datosValidados;
    }

    inicializar() {
        const formulario = document.getElementById('formularioRegistro');
        
        formulario.addEventListener('submit', (evento) => {
            evento.preventDefault();
            this.procesarRegistro();
        });
    }
}

// ===================================
// CLASE HIJA: Alumno
// ===================================
class Alumno extends Usuario {
    #matricula;

    constructor() {
        super('alumno');
    }

    get matricula() { return this.#matricula; }
    set matricula(valor) { this.#matricula = valor?.trim(); }

    capturarDatos() {
        super.capturarDatosBasicos();
        this.#matricula = document.getElementById('matriculaEstudiante').value.trim();
    }

    validarMatricula() {
        if (!this.#matricula || this.#matricula === '') {
            this.mostrarError('matriculaEstudiante', 'La matrícula es obligatoria');
            return false;
        }
        const formatoValidoMatricula = /^\d{6,10}$/;
        if (!formatoValidoMatricula.test(this.#matricula)) {
            this.mostrarError('matriculaEstudiante', 'La matrícula debe tener entre 6 y 10 números');
            return false;
        }
        this.limpiarError('matriculaEstudiante');
        return true;
    }

    validarTodos() {
        let esValido = super.validarTodos();
        if (!this.validarMatricula()) esValido = false;
        return esValido;
    }

    obtenerDatos() {
        const datosBasicos = super.obtenerDatos();
        return {
            ...datosBasicos,
            matricula: this.#matricula
        };
    }

    async procesarRegistro() {
        // Limpiar todos los errores anteriores
        this.limpiarTodosLosErrores();
        
        this.capturarDatos();
        
        if (!this.validarTodos()) {
            this.mostrarMensaje('Por favor corrige los errores en el formulario', 'error');
            return false;
        }

        const datosValidados = this.obtenerDatos();
        console.log('Datos del alumno validados:', datosValidados);
        
        // Mostrar mensaje de "enviando..."
        this.mostrarMensaje('Guardando alumno...', 'info');
        
        // Enviar datos al servidor PHP
        const respuestaServidor = await this.enviarAlServidor(datosValidados);
        
        if (respuestaServidor.exito) {
            this.mostrarMensaje('¡Alumno registrado correctamente!', 'exito');
            // Opcional: limpiar formulario o redirigir
            setTimeout(() => {
                window.location.href = 'loginAlumno.html';
            }, 2000);
        } else {
            this.mostrarMensaje('Error: ' + respuestaServidor.mensaje, 'error');
        }
        
        return datosValidados;
    }

    inicializar() {
        const formulario = document.getElementById('formularioRegistro');
        
        formulario.addEventListener('submit', (evento) => {
            evento.preventDefault();
            this.procesarRegistro();
        });
    }
}

// ===================================
// FUNCIÓN PARA CREAR USUARIOS
// ===================================
function crearUsuario(tipoUsuario) {
    switch(tipoUsuario) {
        case 'administrador':
            return new Administrador();
        case 'docente':
            return new Docente();
        case 'alumno':
            return new Alumno();
        default:
            throw new Error('Tipo de usuario no válido');
    }
}

// ===================================
// INICIALIZACIÓN AUTOMÁTICA
// ===================================
document.addEventListener('DOMContentLoaded', function() {
    // Detectar qué tipo de página de registro es
    const url = window.location.pathname;
    let tipoUsuario;
    
    if (url.includes('registroAdministrador')) {
        tipoUsuario = 'administrador';
    } else if (url.includes('registroDocente')) {
        tipoUsuario = 'docente';
    } else if (url.includes('registroAlumno')) {
        tipoUsuario = 'alumno';
    }
    
    if (tipoUsuario) {
        try {
            const usuario = crearUsuario(tipoUsuario);
            usuario.inicializar();
            console.log(`Sistema de registro ${tipoUsuario} inicializado correctamente`);
        } catch (error) {
            console.error('Error al inicializar el sistema de registro:', error);
        }
    }
});