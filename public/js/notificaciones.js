document.addEventListener('DOMContentLoaded', function() {
    // Obtener datos del usuario desde localStorage
    const datosUsuarioGuardado = localStorage.getItem('usuarioActual');

    if (!datosUsuarioGuardado) {
        mostrarMensaje('Error: No se encontraron datos del usuario', 'error');
        setTimeout(() => {
            window.location.href = '../auth/loginDocente.php';
        }, 2000);
        return;
    }

    const datosUsuario = JSON.parse(datosUsuarioGuardado);
    const docenteId = datosUsuario.id;

    // Funciones de utilidad
    function mostrarMensaje(texto, tipo) {
        const mensajeAnterior = document.querySelector('.mensajeGeneral');
        if (mensajeAnterior) mensajeAnterior.remove();

        const mensaje = document.createElement('div');
        mensaje.className = `mensajeGeneral ${tipo}`;
        mensaje.textContent = texto;
        mensaje.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            z-index: 1000;
            max-width: 300px;
            word-wrap: break-word;
        `;

        if (tipo === 'exito') {
            mensaje.style.backgroundColor = '#4CAF50';
        } else if (tipo === 'error') {
            mensaje.style.backgroundColor = '#f44336';
        } else {
            mensaje.style.backgroundColor = '#2196F3';
        }

        document.body.appendChild(mensaje);

        setTimeout(() => {
            if (mensaje.parentNode) mensaje.remove();
        }, 5000);
    }

    async function enviarAlServidor(datos) {
        try {
            const respuesta = await fetch('../../controllers/NotificationsController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': window.csrfToken
                },
                body: JSON.stringify(datos)
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

    // Función para cargar recursos pendientes
    async function cargarRecursosPendientes() {
        const datosEnvio = {
            accion: 'obtenerRecursosPendientes',
            id_docente: docenteId
        };

        const respuesta = await enviarAlServidor(datosEnvio);

        if (respuesta.exito) {
            mostrarRecursos(respuesta.recursos);
        } else {
            mostrarMensaje('Error al cargar las notificaciones: ' + respuesta.mensaje, 'error');
        }
    }

    // Función para mostrar recursos en la interfaz
    function mostrarRecursos(recursos) {
        const contenedor = document.getElementById('contenedorNotificaciones');
        
        if (recursos.length === 0) {
            contenedor.innerHTML = `
                <div class="sin-notificaciones">
                    <h3>No hay notificaciones pendientes</h3>
                    <p>Todos los recursos de tus alumnos han sido revisados.</p>
                </div>
            `;
            return;
        }

        let html = '<h2>Recursos Pendientes de Aprobación</h2><br><br><br>';
        
        recursos.forEach(recurso => {
            html += `
                <div class="tarjeta-notificacion" data-recurso-id="${recurso.id_recurso}">
                    <div class="info-recurso">
                        <div class="header-recurso">
                            <h3>${recurso.titulo}</h3>
                            <span class="categoria-badge">${recurso.nombre_categoria}</span>
                        </div>
                        <p class="descripcion">${recurso.descripcion}</p>
                        <div class="info-alumno">
                            <strong>Subido por:</strong> ${recurso.nombre_alumno} ${recurso.apellidos_alumno} 
                            <br><strong>Matrícula:</strong> ${recurso.matricula_alumno}
                            <br><strong>Grupo:</strong> ${recurso.nombre_grupo}
                        </div>
                        <div class="enlace-recurso">
                            <strong>Enlace:</strong> 
                            <a href="${recurso.archivo_url}" target="_blank" rel="noopener noreferrer">
                                ${recurso.archivo_url}
                            </a>
                        </div>
                    </div>
                    <div class="botones-accion">
                        <button class="btn-aprobar" onclick="aprobarRecurso(${recurso.id_recurso})">
                            <i class="fas fa-check"></i> Aprobar
                        </button>
                        <button class="btn-rechazar" onclick="mostrarModalRechazo(${recurso.id_recurso})">
                            <i class="fas fa-times"></i> Rechazar
                        </button>
                    </div>
                </div>
            `;
        });

        contenedor.innerHTML = html;
    }

    // Función para aprobar un recurso
    window.aprobarRecurso = async function(idRecurso) {
        const datosEnvio = {
            accion: 'aprobarRecurso',
            id_recurso: idRecurso,
            id_docente: docenteId
        };

        const respuesta = await enviarAlServidor(datosEnvio);

        if (respuesta.exito) {
            mostrarMensaje('Recurso aprobado exitosamente', 'exito');
            // Remover la tarjeta de la interfaz
            const tarjeta = document.querySelector(`[data-recurso-id="${idRecurso}"]`);
            if (tarjeta) {
                tarjeta.remove();
            }
            // Recargar la lista para asegurar consistencia
            setTimeout(() => {
                cargarRecursosPendientes();
            }, 1000);
        } else {
            mostrarMensaje('Error al aprobar el recurso: ' + respuesta.mensaje, 'error');
        }
    };

    // Función para mostrar el modal de rechazo
    window.mostrarModalRechazo = function(idRecurso) {
        const modal = document.getElementById('modalRechazo');
        const overlay = document.getElementById('overlayModal');
        
        modal.style.display = 'block';
        overlay.style.display = 'block';
        
        // Guardar el ID del recurso en el modal
        modal.setAttribute('data-recurso-id', idRecurso);
        
        // Limpiar el textarea
        document.getElementById('motivoRechazo').value = '';
    };

    // Función para cerrar el modal
    window.cerrarModalRechazo = function() {
        const modal = document.getElementById('modalRechazo');
        const overlay = document.getElementById('overlayModal');
        
        modal.style.display = 'none';
        overlay.style.display = 'none';
    };

    // Función para confirmar el rechazo
    window.confirmarRechazo = async function() {
        const modal = document.getElementById('modalRechazo');
        const idRecurso = modal.getAttribute('data-recurso-id');
        const motivo = document.getElementById('motivoRechazo').value.trim();

        if (!motivo) {
            mostrarMensaje('Por favor, ingresa un motivo para el rechazo', 'error');
            return;
        }

        const datosEnvio = {
            accion: 'rechazarRecurso',
            id_recurso: idRecurso,
            id_docente: docenteId,
            motivo: motivo
        };

        const respuesta = await enviarAlServidor(datosEnvio);

        if (respuesta.exito) {
            mostrarMensaje('Recurso rechazado exitosamente', 'exito');
            cerrarModalRechazo();
            
            // Remover la tarjeta de la interfaz
            const tarjeta = document.querySelector(`[data-recurso-id="${idRecurso}"]`);
            if (tarjeta) {
                tarjeta.remove();
            }
            
            // Recargar la lista para asegurar consistencia
            setTimeout(() => {
                cargarRecursosPendientes();
            }, 1000);
        } else {
            mostrarMensaje('Error al rechazar el recurso: ' + respuesta.mensaje, 'error');
        }
    };

    // Cargar recursos al inicio
    cargarRecursosPendientes();

    // Event listener para cerrar modal con la tecla Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            cerrarModalRechazo();
        }
    });
});