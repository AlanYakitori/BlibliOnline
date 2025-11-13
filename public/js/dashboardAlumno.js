document.addEventListener('DOMContentLoaded', function() {
    const usuarioActualStorage = localStorage.getItem('usuarioActual');
    const nombreBienvenida = document.getElementById('nombreBienvenida');

    if (usuarioActualStorage) {
        try {
            const datosUsuario = JSON.parse(usuarioActualStorage);

            const nombre = datosUsuario.nombre.trim();
            nombreBienvenida.textContent = `Bienvenido ${nombre}`;
        } catch (e) {
            console.warn('usuarioActual corrupto en localStorage');
        }
    }

    const datosUsuario = JSON.parse(usuarioActualStorage);
    const id = datosUsuario.id;

    const btnCerrarSesion = document.getElementById('btnCerrarSesion');
    if (btnCerrarSesion) {
        btnCerrarSesion.addEventListener('click', function() {
            cerrarSesion();
        });
    }

    // Al cargar la página, verificar si el alumno tiene grupo
    verificarEstadoGrupo();

    async function verificarEstadoGrupo() {
        try {
            const datos = {
                accion: 'verificarGrupoAlumno',
                idAlumno: id
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
                const btnUnirmeGrupo = document.getElementById('unirmeGrupo');
                if (btnUnirmeGrupo) {
                    if (resultado.tieneGrupo) {
                        // Cambiar texto y funcionalidad a "Ver Grupo"
                        btnUnirmeGrupo.textContent = 'Ver Grupo';
                        btnUnirmeGrupo.onclick = function() { verGrupo(); };
                    } else {
                        // Mantener funcionalidad original "Unirme a Grupo"
                        btnUnirmeGrupo.textContent = 'Unirme a grupo';
                        btnUnirmeGrupo.onclick = function() { unirmeGrupo(); };
                    }
                }
            }
        } catch (error) {
            console.error('Error al verificar estado del grupo:', error);
        }
    }

    async function cerrarSesion() {
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
                window.history.pushState(null, '', window.location.href);
                window.onpopstate = function () { window.history.pushState(null, '', window.location.href); };
                alert('Sesión cerrada exitosamente');
                window.location.href = '../../../index.php';
            } else {
                alert('Error al cerrar sesión: ' + (resultado.mensaje || ''));
            }
        } catch (error) {
            console.error('Error al cerrar sesión:', error);
            try { localStorage.clear(); } catch(e){}
            window.location.href = '../../../index.php';
        }
    }

    // Función para mostrar mensaje general (igual que dashboardDocente)
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

    // Función para mostrar formulario de unirse a grupo
    function unirmeGrupo() {
        const contenedorFormulario = document.getElementById('contenedorFormularioGrupo');
        
        if (!contenedorFormulario) {
            // Si no está el contenedor, usar el modal como fallback
            mostrarModalUnirse();
            return;
        }

        // Crear el formulario en la página principal
        contenedorFormulario.innerHTML = `
            <div class="formulario-unirse-grupo">
                <h3>Unirse a Grupo</h3>
                <form id="formularioUnirseGrupo" class="formularioGrupo">
                    <label for="codigoGrupo">Código de acceso (8 caracteres):</label>
                    <input type="text" id="codigoGrupo" maxlength="8" required placeholder="Ej: ABCD1234">
                    <div class="botones-formulario">
                        <input type="submit" value="Unirse al Grupo" class="btnGenerar">
                        <button type="button" onclick="cancelarFormulario()" class="btnCancelar">Cancelar</button>
                    </div>
                </form>
            </div>
        `;

        // Scroll hasta el formulario
        contenedorFormulario.scrollIntoView({ behavior: 'smooth' });

        // Event listener para el formulario
        const formulario = document.getElementById('formularioUnirseGrupo');
        formulario.addEventListener('submit', function(e) {
            e.preventDefault();
            enviarCodigoGrupo();
        });
    }

    // Función para mostrar modal como fallback
    function mostrarModalUnirse() {
        // Crear overlay para el formulario modal
        const overlay = document.createElement('div');
        overlay.className = 'modal-overlay';
        overlay.innerHTML = `
            <div class="modal-formulario">
                <h3>Unirse a Grupo</h3>
                <form id="formularioUnirseGrupo">
                    <label for="codigoGrupo">Código de acceso (8 caracteres):</label>
                    <input type="text" id="codigoGrupo" maxlength="8" required placeholder="Ej: ABCD1234">
                    <div class="botones-modal">
                        <button type="submit" class="btn-unirse">Unirse</button>
                        <button type="button" class="btn-cancelar" onclick="cerrarModal()">Cancelar</button>
                    </div>
                </form>
            </div>
        `;

        document.body.appendChild(overlay);

        // Event listener para el formulario
        const formulario = document.getElementById('formularioUnirseGrupo');
        formulario.addEventListener('submit', function(e) {
            e.preventDefault();
            enviarCodigoGrupo();
        });
    }

    // Función para cancelar formulario en página principal
    window.cancelarFormulario = function() {
        const contenedorFormulario = document.getElementById('contenedorFormularioGrupo');
        if (contenedorFormulario) {
            contenedorFormulario.innerHTML = '';
        }
    }

    // Función para cerrar modal
    window.cerrarModal = function() {
        const overlay = document.querySelector('.modal-overlay');
        if (overlay) {
            overlay.remove();
        }
    }

    // Función para enviar código y unirse al grupo
    async function enviarCodigoGrupo() {
        const codigoGrupo = document.getElementById('codigoGrupo').value.trim().toUpperCase();

        if (codigoGrupo.length !== 8) {
            mostrarMensaje('El código debe tener exactamente 8 caracteres', 'error');
            return;
        }

        try {
            mostrarMensaje('Verificando código...', 'info');
            
            const datos = {
                accion: 'unirseGrupo',
                codigoGrupo: codigoGrupo,
                idAlumno: id
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
                mostrarMensaje(resultado.mensaje, 'exito');
                cerrarModal();
                
                // Actualizar estado después de unirse
                setTimeout(() => {
                    verificarEstadoGrupo();
                }, 2000);
            } else {
                mostrarMensaje(resultado.mensaje, 'error');
            }
        } catch (error) {
            console.error('Error al unirse al grupo:', error);
            mostrarMensaje('Error al conectar con el servidor', 'error');
        }
    }

    // Función para ir a ver grupo
    function verGrupo() {
        window.location.href = 'panelGestionAlumno.php';
    }

    // Función para mostrar información del grupo (para panelGestionAlumno.php)
    async function cargarInfoGrupo() {
        try {
            const datos = {
                accion: 'verGrupoAlumno',
                idAlumno: id
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
                mostrarInfoGrupo(resultado.grupo);
            } else {
                mostrarMensaje(resultado.mensaje, 'error');
                setTimeout(() => {
                    window.location.href = 'alumno.php';
                }, 2000);
            }
        } catch (error) {
            console.error('Error al cargar información del grupo:', error);
            mostrarMensaje('Error al conectar con el servidor', 'error');
        }
    }

    // Función para mostrar información del grupo
    function mostrarInfoGrupo(grupo) {
        const contenedor = document.getElementById('contenedorInfoGrupo');
        if (!contenedor) return;

        contenedor.innerHTML = `
            <div class="grupo-item">
                <div class="grupo-header">
                    <div class="grupo-info">
                        <h3>${grupo.nombre}</h3>
                        <p><strong>Código de acceso:</strong> ${grupo.clave}</p>
                        <p><strong>Total miembros:</strong> ${grupo.total_miembros}</p>
                    </div>
                    <div class="grupo-acciones">
                        <a href="#" class="enlace-salir" onclick="salirDelGrupo()">Salir del Grupo</a>
                    </div>
                </div>
                
                <div class="docente-info">
                    <h4>Docente encargado:</h4>
                    <p><strong>Nombre:</strong> ${grupo.docente.nombre} ${grupo.docente.apellidos}</p>
                    <p><strong>Email:</strong> ${grupo.docente.correo}</p>
                </div>

                <div class="miembros-lista">
                    <h4>Miembros del grupo:</h4>
                    ${grupo.miembros.length > 0 ? 
                        grupo.miembros.map(miembro => `
                            <div class="miembro-item">
                                <div class="miembro-info">
                                    <span class="miembro-nombre">${miembro.nombre} ${miembro.apellidos}</span>
                                    <span class="miembro-email">(${miembro.correo})</span>
                                </div>
                            </div>
                        `).join('') 
                        : '<p class="sin-miembros">No hay otros miembros en este grupo.</p>'
                    }
                </div>
            </div>
        `;
    }

    // Función para mostrar botón de salir del grupo (ya no se usa por separado)
    function mostrarBotonSalir() {
        // Esta función ya no es necesaria porque el botón se incluye en mostrarInfoGrupo
    }

    // Función para salir del grupo
    window.salirDelGrupo = async function() {
        if (confirm('¿Estás seguro de que deseas salir del grupo? Esta acción no se puede deshacer.')) {
            try {
                mostrarMensaje('Saliendo del grupo...', 'info');
                
                const datos = {
                    accion: 'salirDeGrupo',
                    idAlumno: id
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
                    mostrarMensaje(resultado.mensaje, 'exito');
                    
                    // Redirigir al dashboard principal
                    setTimeout(() => {
                        window.location.href = 'alumno.php';
                    }, 2000);
                } else {
                    mostrarMensaje(resultado.mensaje, 'error');
                }
            } catch (error) {
                console.error('Error al salir del grupo:', error);
                mostrarMensaje('Error al conectar con el servidor', 'error');
            }
        }
    }

    // Si estamos en panelGestionAlumno.php, cargar información del grupo
    if (window.location.pathname.includes('panelGestionAlumno.php')) {
        cargarInfoGrupo();
    }

    window.history.pushState(null, '', window.location.href);
    window.onpopstate = function () { window.history.pushState(null, '', window.location.href); };
});