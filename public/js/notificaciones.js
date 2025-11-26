document.addEventListener('DOMContentLoaded', function() {
    // Obtener datos del usuario desde localStorage
    const datosUsuarioGuardado = localStorage.getItem('usuarioActual');

    if (!datosUsuarioGuardado) {
        mostrarMensaje('Error: No se encontraron datos del usuario', 'error');
        setTimeout(() => {
            // Redirigir según el tipo de usuario
            const currentPath = window.location.pathname;
            if (currentPath.includes('docente')) {
                window.location.href = '../auth/loginDocente.php';
            } else if (currentPath.includes('alumno')) {
                window.location.href = '../auth/loginAlumno.php';
            } else {
                window.location.href = '../auth/loginAdministrador.php';
            }
        }, 2000);
        return;
    }

    const datosUsuario = JSON.parse(datosUsuarioGuardado);
    const usuarioId = datosUsuario.id;
    const tipoUsuario = datosUsuario.tipoUsuario;
    
    // Configurar navegación según tipo de usuario
    configurarNavegacion(tipoUsuario, usuarioId);

    // Inicializar notificaciones según tipo de usuario
    inicializarNotificaciones(tipoUsuario, usuarioId);

    // Funciones de utilidad
    function mostrarMensaje(texto, tipo) {
        const mensajeAnterior = document.querySelector('.mensajeGeneral');
        if (mensajeAnterior) mensajeAnterior.remove();

        const mensaje = document.createElement('div');
        mensaje.className = `mensajeGeneral ${tipo}`;
        mensaje.textContent = texto;

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

    // Inicializar según tipo de usuario
    function inicializarNotificaciones(tipoUsuario, usuarioId) {
        switch(tipoUsuario.toLowerCase()) {
            case 'docente':
                inicializarNotificacionesDocente(usuarioId);
                break;
            case 'alumno':
                inicializarNotificacionesAlumno(usuarioId);
                break;
            case 'administrador':
                inicializarNotificacionesAdministrador();
                break;
            default:
                mostrarMensaje('Tipo de usuario no reconocido', 'error');
        }
    }

    // ===== NOTIFICACIONES PARA DOCENTES =====
    function inicializarNotificacionesDocente(docenteId) {
        cargarRecursosPendientes(docenteId);
        
        // Configurar eventos para modales
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                cerrarModalRechazo();
            }
        });
    }

    async function cargarRecursosPendientes(docenteId) {
        const datos = {
            accion: 'obtenerRecursosPendientes',
            id_docente: docenteId
        };

        const resultado = await enviarAlServidor(datos);

        if (resultado.exito) {
            mostrarRecursosDocente(resultado.recursos);
        } else {
            mostrarMensaje(resultado.mensaje, 'error');
        }
    }

    function mostrarRecursosDocente(recursos) {
        const contenedorNotificaciones = document.getElementById('contenedorNotificaciones');
        
        if (!recursos || recursos.length === 0) {
            contenedorNotificaciones.innerHTML = `
                <div class="sin-notificaciones">
                    <h3>No hay recursos pendientes por revisar</h3>
                    <p>Todos los recursos han sido procesados.</p>
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
                    </div>
                    <div class="botones-accion">
                        <button onclick="aprobarRecurso(${recurso.id_recurso})" class="btn-aprobar">
                            <i class="fa-solid fa-check"></i> Aprobar
                        </button>
                        <button onclick="mostrarModalRechazo(${recurso.id_recurso})" class="btn-rechazar">
                            <i class="fa-solid fa-times"></i> Rechazar
                        </button>
                    </div>
                </div>
            `;
        });

        contenedorNotificaciones.innerHTML = html;
    }

    window.aprobarRecurso = async function(idRecurso) {
        const datos = {
            accion: 'aprobarRecurso',
            id_recurso: idRecurso,
            id_docente: usuarioId
        };

        const resultado = await enviarAlServidor(datos);

        if (resultado.exito) {
            mostrarMensaje('Recurso aprobado correctamente', 'exito');
            cargarRecursosPendientes(usuarioId);
        } else {
            mostrarMensaje(resultado.mensaje, 'error');
        }
    };

    window.mostrarModalRechazo = function(idRecurso) {
        document.getElementById('recursoIdRechazo').value = idRecurso;
        document.getElementById('modalRechazo').style.display = 'flex';
    };

    window.cerrarModalRechazo = function() {
        document.getElementById('modalRechazo').style.display = 'none';
        document.getElementById('motivoRechazo').value = '';
        document.getElementById('recursoIdRechazo').value = '';
    };

    window.confirmarRechazo = async function() {
        const idRecurso = document.getElementById('recursoIdRechazo').value;
        const motivo = document.getElementById('motivoRechazo').value;

        if (!motivo.trim()) {
            mostrarMensaje('Por favor, ingrese un motivo para el rechazo', 'error');
            return;
        }

        const datos = {
            accion: 'rechazarRecurso',
            id_recurso: idRecurso,
            id_docente: usuarioId,
            motivo: motivo
        };

        const resultado = await enviarAlServidor(datos);

        if (resultado.exito) {
            mostrarMensaje('Recurso rechazado correctamente', 'exito');
            cerrarModalRechazo();
            cargarRecursosPendientes(usuarioId);
        } else {
            mostrarMensaje(resultado.mensaje, 'error');
        }
    };

    // ===== NOTIFICACIONES PARA ALUMNOS =====
    function inicializarNotificacionesAlumno(alumnoId) {
        cargarNotificacionesAlumno(alumnoId);
    }

    async function cargarNotificacionesAlumno(alumnoId) {
        const datos = {
            accion: 'obtenerRecursosAlumno',
            id_alumno: alumnoId
        };

        const resultado = await enviarAlServidor(datos);

        if (resultado.exito) {
            mostrarNotificacionesAlumno(resultado.recursos);
        } else {
            mostrarMensaje(resultado.mensaje, 'error');
        }
    }

    function mostrarNotificacionesAlumno(recursos) {
        const contenedorNotificaciones = document.getElementById('contenedorNotificaciones');
        
        if (!recursos || recursos.length === 0) {
            contenedorNotificaciones.innerHTML = `
                <div class="sin-notificaciones">
                    <h3>No has subido ningún recurso</h3>
                    <p>Cuando subas contenido, aquí verás el estado de tus recursos.</p>
                </div>
            `;
            return;
        }

        let html = '<h2>Estado de tus Recursos</h2><br><br><br>';
        
        recursos.forEach(recurso => {
            let estadoClass = '';
            let estadoTexto = '';
            let estadoIcono = '';
            let motivoRechazoHtml = '';

            if (recurso.aprobado === 1) {
                estadoClass = 'estado-aprobado';
                estadoTexto = 'Aprobado';
                estadoIcono = '<i class="fa-solid fa-check-circle"></i>';
            } else if (recurso.aprobado === 0) {
                estadoClass = 'estado-rechazado';
                estadoTexto = 'Rechazado';
                estadoIcono = '<i class="fa-solid fa-times-circle"></i>';
                
                // Mostrar motivo del rechazo si está disponible
                if (recurso.motivo_rechazo) {
                    motivoRechazoHtml = `
                        <div class="motivo-rechazo">
                            <strong><i class="fa-solid fa-exclamation-triangle"></i> Motivo del rechazo:</strong>
                            <p>${recurso.motivo_rechazo}</p>
                        </div>
                    `;
                }
            } else {
                estadoClass = 'estado-pendiente';
                estadoTexto = 'Pendiente de revisión';
                estadoIcono = '<i class="fa-solid fa-clock"></i>';
            }

            html += `
                <div class="tarjeta-notificacion tarjeta-alumno">
                    <div class="info-recurso">
                        <div class="header-recurso">
                            <h3>${recurso.titulo}</h3>
                            <span class="estado-badge ${estadoClass}">
                                ${estadoIcono} ${estadoTexto}
                            </span>
                        </div>
                        <p class="descripcion">${recurso.descripcion}</p>
                        
                        <div class="info-recurso-meta">
                            <strong>Categoría:</strong> ${recurso.categoria || 'Sin categoría'}<br>
                            <strong>ID del recurso:</strong> ${recurso.id_recurso}
                            ${recurso.calificacion ? `<br><strong>Calificación:</strong> ${recurso.calificacion}/10` : ''}
                        </div>
                        
                        ${recurso.archivo_url ? `
                            <div class="enlace-recurso">
                                <strong>Enlace:</strong> <a href="${recurso.archivo_url}" target="_blank">${recurso.archivo_url}</a>
                            </div>
                        ` : ''}
                        
                        ${motivoRechazoHtml}
                    </div>
                </div>
            `;
        });

        contenedorNotificaciones.innerHTML = html;
    }

    // ===== NOTIFICACIONES PARA ADMINISTRADORES =====
    function inicializarNotificacionesAdministrador() {
        cargarUsuariosPendientes();
        
        // Configurar eventos para modales
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                cerrarModalRechazoUsuario();
            }
        });
    }

    async function cargarUsuariosPendientes() {
        const datos = {
            accion: 'obtenerUsuariosPendientes'
        };

        const resultado = await enviarAlServidor(datos);

        if (resultado.exito) {
            mostrarUsuariosPendientes(resultado.usuarios);
        } else {
            mostrarMensaje(resultado.mensaje, 'error');
        }
    }

    function mostrarUsuariosPendientes(usuarios) {
        const contenedorNotificaciones = document.getElementById('contenedorNotificaciones');
        
        if (!usuarios || usuarios.length === 0) {
            contenedorNotificaciones.innerHTML = `
                <div class="sin-notificaciones">
                    <h3>No hay usuarios pendientes por aprobar</h3>
                    <p>Todos los registros han sido procesados.</p>
                </div>
            `;
            return;
        }

        let html = '<h2>Usuarios Pendientes de Aprobación</h2><br><br><br>';
        
        usuarios.forEach(usuario => {
            html += `
                <div class="tarjeta-notificacion tarjeta-administrador">
                    <div class="info-recurso">
                        <div class="header-recurso">
                            <h3>${usuario.nombre} ${usuario.apellidos}</h3>
                            <span class="tipo-usuario-badge">${usuario.tipoUsuario}</span>
                        </div>
                        <div class="info-usuario">
                            <p><strong>Email:</strong> ${usuario.correo}</p>
                            <p><strong>Fecha de nacimiento:</strong> ${usuario.fechaNacimiento}</p>
                            <p><strong>Estado:</strong> ${usuario.aceptado ? 'Aceptado' : 'Pendiente'}</p>
                        </div>
                    </div>
                    <div class="botones-accion">
                        <button onclick="aprobarUsuario(${usuario.id_usuario})" class="btn-aprobar">
                            <i class="fa-solid fa-check"></i> Aprobar Usuario
                        </button>
                        <button onclick="mostrarModalRechazoUsuario(${usuario.id_usuario})" class="btn-rechazar">
                            <i class="fa-solid fa-times"></i> Rechazar Usuario
                        </button>
                    </div>
                </div>
            `;
        });

        contenedorNotificaciones.innerHTML = html;
    }

    window.aprobarUsuario = async function(idUsuario) {
        const datos = {
            accion: 'aprobarUsuario',
            id_usuario: idUsuario
        };

        const resultado = await enviarAlServidor(datos);

        if (resultado.exito) {
            mostrarMensaje('Usuario aprobado correctamente', 'exito');
            cargarUsuariosPendientes();
        } else {
            mostrarMensaje(resultado.mensaje, 'error');
        }
    };

    window.mostrarModalRechazoUsuario = function(idUsuario) {
        document.getElementById('usuarioIdRechazo').value = idUsuario;
        document.getElementById('modalRechazoUsuario').style.display = 'flex';
    };

    window.cerrarModalRechazoUsuario = function() {
        document.getElementById('modalRechazoUsuario').style.display = 'none';
        document.getElementById('motivoRechazoUsuario').value = '';
        document.getElementById('usuarioIdRechazo').value = '';
    };

    window.confirmarRechazoUsuario = async function() {
        const idUsuario = document.getElementById('usuarioIdRechazo').value;
        const motivo = document.getElementById('motivoRechazoUsuario').value;

        if (!motivo.trim()) {
            mostrarMensaje('Por favor, ingrese un motivo para el rechazo', 'error');
            return;
        }

        const datos = {
            accion: 'rechazarUsuario',
            id_usuario: idUsuario,
            motivo: motivo
        };

        const resultado = await enviarAlServidor(datos);

        if (resultado.exito) {
            mostrarMensaje('Usuario rechazado y notificado correctamente', 'exito');
            cerrarModalRechazoUsuario();
            cargarUsuariosPendientes();
        } else {
            mostrarMensaje(resultado.mensaje, 'error');
        }
    };

    // ===== FUNCIONES DEL MODAL DE RECHAZO DE USUARIO =====
    // (Las funciones están definidas arriba dentro del contexto del administrador)

    // ===== FUNCIONES DEL MODAL DE RECHAZO DE RECURSO =====
    // (Las funciones están definidas arriba dentro del contexto del docente)

    // ===== FUNCIONES DE UTILIDAD =====
    function formatearFecha(fechaStr) {
        const fecha = new Date(fechaStr);
        return fecha.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
});

// Función para configurar navegación según tipo de usuario
async function configurarNavegacion(tipoUsuario, idUsuario) {
    if (tipoUsuario === 'alumno') {
        await verificarEstadoGrupoAlumno(idUsuario);
    }
    // Para administrador y docente, la navegación es estática y ya está definida en los archivos PHP
}

// Función para verificar estado del grupo del alumno
async function verificarEstadoGrupoAlumno(idAlumno) {
    try {
        const datos = {
            accion: 'verificarGrupoAlumno',
            idAlumno: idAlumno
        };

        const respuesta = await fetch('../../controllers/GrupoController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datos)
        });
        
        const resultado = await respuesta.json();
        
        if (resultado.exito) {
            // Actualizar navegación del alumno
            actualizarNavegacionAlumno(resultado.tieneGrupo);
        }
    } catch (error) {
        console.error('Error al verificar estado del grupo:', error);
        // En caso de error, generar navegación básica para alumno
        const navListAlumno = document.getElementById('navListAlumno');
        if (navListAlumno) {
            navListAlumno.innerHTML = `
                <li><a href="alumno.php">Home</a></li>
                <li><a href="panelGestionContenidoAlumno.php">Subir Contenido</a></li>
                <li><a href="notificacionesAlumno.php">Notificaciones</a></li>
                <li><a href="perfilAlumno.php">Mi cuenta</a></li>
                <li><a href="#" class="lnk" id="btnCerrarSesion">Cerrar Sesión</a></li>
            `;
            configurarCerrarSesionCompleto();
        }
    }
}

// Función para actualizar la navegación del alumno
function actualizarNavegacionAlumno(tieneGrupo) {
    // Buscar el enlace de "Unirme a grupo" en la navegación
    const btnUnirmeGrupo = document.getElementById('unirmeGrupo');
    const navListAlumno = document.getElementById('navListAlumno');
    
    if (btnUnirmeGrupo) {
        if (tieneGrupo) {
            // Cambiar texto y funcionalidad a "Ver Grupo"
            btnUnirmeGrupo.textContent = 'Ver Grupo';
            btnUnirmeGrupo.onclick = function() { 
                window.location.href = 'panelGestionAlumno.php';
            };
        } else {
            // Mantener funcionalidad original "Unirme a Grupo"
            btnUnirmeGrupo.textContent = 'Unirme a grupo';
            // La funcionalidad de unirse está manejada por dashboardAlumno.js
        }
    } else if (navListAlumno) {
        // Si estamos en perfilAlumno.php, generar la navegación dinámicamente
        if (tieneGrupo) {
            navListAlumno.innerHTML = `
                <li><a href="#" onclick="window.location.href='panelGestionAlumno.php'">Ver Grupo</a></li>
                <li><a href="panelGestionContenidoAlumno.php">Subir Contenido</a></li>
                <li><a href="notificacionesAlumno.php">Notificaciones</a></li>
                <li><a href="perfilAlumno.php">Mi cuenta</a></li>
                <li><a href="#" class="lnk" id="btnCerrarSesion">Cerrar Sesión</a></li>
            `;
        } else {
            navListAlumno.innerHTML = `
                <li><a href="alumno.php" id="unirmeGrupo">Unirme a grupo</a></li>
                <li><a href="panelGestionContenidoAlumno.php">Subir Contenido</a></li>
                <li><a href="notificacionesAlumno.php">Notificaciones</a></li>
                <li><a href="perfilAlumno.php">Mi cuenta</a></li>
                <li><a href="#" class="lnk" id="btnCerrarSesion">Cerrar Sesión</a></li>
            `;
        }
        // Reconfigurar eventos después de actualizar el HTML
        configurarCerrarSesionCompleto();
    }
}

// Función para configurar el cerrar sesión con validaciones completas
function configurarCerrarSesionCompleto() {
    const btnCerrarSesion = document.getElementById('btnCerrarSesion'); 
    if (btnCerrarSesion) {
        // Remover event listeners previos para evitar duplicados
        const nuevoBtn = btnCerrarSesion.cloneNode(true);
        btnCerrarSesion.parentNode.replaceChild(nuevoBtn, btnCerrarSesion);
        
        // Agregar nuevo event listener
        nuevoBtn.addEventListener('click', function(e) {
            e.preventDefault();
            cerrarSesionCompleta();
        });
    }
}

// Función para cerrar sesión con validaciones completas
async function cerrarSesionCompleta() {
    try {
        if (!confirm('¿Está seguro de que desea cerrar sesión?')) return;

        const datosLogout = { csrf_token: window.csrfToken || '' };

        const respuesta = await fetch('../../controllers/LogoutController.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datosLogout)
        });

        const resultado = await respuesta.json();

        if (resultado.exito) {
            try { localStorage.removeItem('usuarioActual'); } catch(e){}
            try { localStorage.clear(); } catch(e){}
                    
            // Proteger contra retroceso del navegador
            window.history.pushState(null, '', window.location.href);
            window.onpopstate = function () { window.history.pushState(null, '', window.location.href); };

            alert('Sesión cerrada exitosamente');
            window.location.href = '../../../index.php';
        } else {
            alert('Error al cerrar sesión: ' + (resultado.mensaje || '')); 
        }

    } catch (error) {
        console.error('Error al cerrar sesión:', error);
        alert('Error de conexión al cerrar sesión');
    }
}

// Funciones auxiliares (deberían existir en el contexto)
function configurarCerrarSesion() {
    configurarCerrarSesionCompleto();
}

function configurarEnlacesPerfil(tipoUsuario) {
    // Configurar enlaces específicos del perfil según tipo de usuario
    console.log(`Configurando enlaces para ${tipoUsuario}`);
}