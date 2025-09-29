// ===================================
// DASHBOARD ALUMNO - LÓGICA
// ===================================

/**
 * Clase para manejar el dashboard del alumno
 */
class DashboardAlumno {
    constructor() {
        this.usuario = null;
        this.inicializar();
    }

    /**
     * Inicializar el dashboard
     */
    inicializar() {
        document.addEventListener('DOMContentLoaded', () => {
            this.cargarDatosUsuario();
        });
    }

    /**
     * Cargar datos del usuario desde localStorage
     */
    cargarDatosUsuario() {
        const usuarioGuardado = localStorage.getItem('usuarioActual');
        
        if (usuarioGuardado) {
            this.usuario = JSON.parse(usuarioGuardado);
            
            // Verificar que sea un alumno
            if (this.usuario.tipoUsuario !== 'alumno') {
                this.mostrarError('Acceso no autorizado');
                this.redirigirA('index.html');
                return;
            }
            
            // Mostrar datos del usuario en la interfaz
            this.mostrarDatosUsuario();
            this.mostrarMensajeBienvenida();
            
        } else {
            // No hay usuario logueado, redirigir al login
            this.mostrarError('Debes iniciar sesión primero');
            this.redirigirA('loginAlumno.html');
        }
    }

    /**
     * Mostrar datos del usuario en la interfaz
     */
    mostrarDatosUsuario() {
        const nombreCompleto = `${this.usuario.nombre} ${this.usuario.apellidos}`;
        
        // Actualizar elementos del DOM
        this.actualizarElemento('nombreUsuario', nombreCompleto);
        this.actualizarElemento('correoUsuario', this.usuario.correo);
        
        // Mostrar matrícula si existe
        const matricula = this.usuario.datosCompletos?.matricula || 'No especificada';
        this.actualizarElemento('matriculaUsuario', matricula);
    }

    /**
     * Actualizar contenido de un elemento del DOM
     */
    actualizarElemento(id, contenido) {
        const elemento = document.getElementById(id);
        if (elemento) {
            elemento.textContent = contenido;
        } else {
            console.warn(`Elemento con ID '${id}' no encontrado`);
        }
    }

    /**
     * Mostrar mensaje de bienvenida en consola
     */
    mostrarMensajeBienvenida() {
        console.log(`Dashboard cargado para: ${this.usuario.nombre} ${this.usuario.apellidos}`);
        console.log('Datos del alumno:', this.usuario);
    }

    /**
     * Cerrar sesión del usuario
     */
    cerrarSesion() {
        try {
            // Confirmar acción
            const confirmar = confirm('¿Estás seguro de que deseas cerrar sesión?');
            
            if (confirmar) {
                // Limpiar datos de sesión
                localStorage.removeItem('usuarioActual');
                
                // Mostrar mensaje de confirmación
                this.mostrarExito('Sesión cerrada correctamente');
                
                // Redirigir a la página principal
                setTimeout(() => {
                    this.redirigirA('index.html');
                }, 1500);
            }
        } catch (error) {
            console.error('Error al cerrar sesión:', error);
            this.mostrarError('Error al cerrar sesión');
        }
    }

    /**
     * Mostrar mensaje de error
     */
    mostrarError(mensaje) {
        alert(`❌ Error: ${mensaje}`);
        console.error('Error:', mensaje);
    }

    /**
     * Mostrar mensaje de éxito
     */
    mostrarExito(mensaje) {
        alert(`✅ ${mensaje}`);
        console.log('Éxito:', mensaje);
    }

    /**
     * Redirigir a otra página
     */
    redirigirA(url) {
        window.location.href = url;
    }

    /**
     * Obtener información del usuario actual
     */
    obtenerUsuario() {
        return this.usuario;
    }

    /**
     * Verificar si el usuario está autenticado
     */
    estaAutenticado() {
        return this.usuario !== null && this.usuario.tipoUsuario === 'alumno';
    }
}

// ===================================
// FUNCIONES GLOBALES
// ===================================

/**
 * Función global para cerrar sesión (llamada desde el HTML)
 */
function cerrarSesion() {
    if (window.dashboardAlumno) {
        window.dashboardAlumno.cerrarSesion();
    } else {
        // Fallback si no existe la instancia
        localStorage.removeItem('usuarioActual');
        alert('Sesión cerrada');
        window.location.href = 'index.html';
    }
}

// ===================================
// INICIALIZACIÓN
// ===================================

// Crear instancia global del dashboard
window.dashboardAlumno = new DashboardAlumno();

// Log de inicialización
console.log('Dashboard de Alumno inicializado correctamente');