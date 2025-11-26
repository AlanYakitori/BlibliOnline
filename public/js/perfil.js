document.addEventListener('DOMContentLoaded', function() {
    // Obtener datos del usuario desde localStorage
    const usuarioActualStorage = localStorage.getItem('usuarioActual');
    let datosUsuario = null;
    
    datosUsuario = JSON.parse(usuarioActualStorage);

    // Si no tenemos datos del usuario, no podemos continuar
    if (!datosUsuario) return;

    const id = datosUsuario.id;
    const tipoUsuario = datosUsuario.tipoUsuario;

    // Configurar navegación específica según tipo de usuario
    configurarNavegacion(tipoUsuario, id);

    // Configurar cerrar sesión
    configurarCerrarSesion();

    // ===== FUNCIONALIDAD DEL PERFIL ADMINISTRADOR =====
    initPerfilAdministrador();
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
        configurarCerrarSesion();
        configurarEnlacesPerfil('alumno');
    }
}

// Función para configurar el cerrar sesión con validaciones completas
function configurarCerrarSesion() {
    const btnCerrarSesion = document.getElementById('btnCerrarSesion'); 
    if (btnCerrarSesion) {
        // Remover event listeners previos para evitar duplicados
        const nuevoBtn = btnCerrarSesion.cloneNode(true);
        btnCerrarSesion.parentNode.replaceChild(nuevoBtn, btnCerrarSesion);
        
        // Agregar nuevo event listener
        nuevoBtn.addEventListener('click', function(e) {
            e.preventDefault();
            cerrarSesion();
        });
    }
}

// Función para cerrar sesión con validaciones completas
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

// ===== FUNCIONALIDAD ESPECÍFICA DEL PERFIL ADMINISTRADOR =====

function initPerfilAdministrador() {
    // Solo ejecutar si estamos en una página con elementos de perfil administrador
    const form = document.getElementById('form-actualizar-datos');
    if (!form) return; // No estamos en perfil de administrador

    // Referencias del Modal y Formulario
    const mensajePerfil = document.getElementById('mensaje-perfil');
    const inputId = document.getElementById('perfil-id');
    const inputNombreForm = document.getElementById('perfil-nombre-input');
    const inputApellidosForm = document.getElementById('perfil-apellidos-input');
    const inputCorreoForm = document.getElementById('perfil-correo-input');
    const inputTelefonoForm = document.getElementById('perfil-telefono-input');
    const inputPass1 = document.getElementById('perfil-pass1');
    const inputPass2 = document.getElementById('perfil-pass2');

    // Referencias de la Vista
    const vistaNombre = document.getElementById('vista-nombre');
    const vistaApellidos = document.getElementById('vista-apellidos');
    const vistaCorreo = document.getElementById('vista-correo');
    const vistaTelefono = document.getElementById('vista-telefono');

    // Referencias de Favoritos
    const contenedorFavoritos = document.getElementById('contenedor-favoritos');

    // Referencias del Modal
    const modal = document.getElementById('modal-actualizar');
    const btnAbrirModal = document.getElementById('btn-abrir-modal');
    const btnCerrarModal = modal ? modal.querySelector('.modal-cerrar') : null;

    // 1. Cargar Datos
    function cargarDatosUsuario() {
        const usuarioStorage = localStorage.getItem('usuarioActual');
        if (!usuarioStorage) return;
        
        try {
            const usuario = JSON.parse(usuarioStorage);
            if (vistaNombre) vistaNombre.textContent = usuario.nombre;
            if (vistaApellidos) vistaApellidos.textContent = usuario.apellidos;
            if (vistaCorreo) vistaCorreo.textContent = usuario.correo;
            if (vistaTelefono) vistaTelefono.textContent = usuario.telefono || 'No registrado';
            
            if (inputId) inputId.value = usuario.id;
            if (inputNombreForm) inputNombreForm.value = usuario.nombre;
            if (inputApellidosForm) inputApellidosForm.value = usuario.apellidos;
            if (inputCorreoForm) inputCorreoForm.value = usuario.correo;
            if (inputTelefonoForm) inputTelefonoForm.value = usuario.telefono || '';
        } catch (e) { 
            console.error('Error LS', e); 
        }
    }

    // 2. Cargar Favoritos
    async function cargarFavoritos() {
        if (!contenedorFavoritos) return;
        
        contenedorFavoritos.innerHTML = '<p style="text-align:center; color:#888;">Cargando...</p>';
        try {
            const token = window.csrfToken || '';
            const respuesta = await fetch('../../controllers/AuthController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': token },
                body: JSON.stringify({ accion: 'obtenerMisFavoritos' })
            });
            
            const resultado = await respuesta.json();
            
            if (resultado.exito && resultado.favoritos) {
                pintarFavoritos(resultado.favoritos);
            } else {
                contenedorFavoritos.innerHTML = '<p style="text-align:center;">No tienes favoritos guardados.</p>';
            }
        } catch (error) {
            contenedorFavoritos.innerHTML = '<p style="text-align:center; color:red;">Error al cargar favoritos.</p>';
        }
    }

    function pintarFavoritos(favoritos) {
        if (!contenedorFavoritos) return;
        
        contenedorFavoritos.innerHTML = ''; 
        
        if (favoritos.length === 0) {
            contenedorFavoritos.innerHTML = '<p style="text-align:center;">No tienes favoritos guardados.</p>';
            return;
        }

        favoritos.forEach(fav => {
            const urlLimpia = fav.archivo_url || '#';
            const urlTexto = urlLimpia.length > 40 ? urlLimpia.substring(0, 40) + '...' : urlLimpia;

            const itemHtml = `
                <div class="item-favorito" id="fav-item-${fav.id_recurso}">
                    <div class="info-favorito">
                        <h3>${fav.titulo}</h3>
                        <small title="${urlLimpia}">
                            <i class="fa-solid fa-link"></i> ${urlTexto}
                        </small>
                    </div>
                    <div class="acciones-favorito" style="display:flex; gap:10px;">
                        <a href="${urlLimpia}" target="_blank" class="btn-ir-recurso" title="Ir al recurso">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                        <button class="btn-eliminar-fav" data-id="${fav.id_recurso}" title="Eliminar de favoritos" style="border:none; background:none; cursor:pointer; color: #dc3545;">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            contenedorFavoritos.innerHTML += itemHtml;
        });
    }

    if (contenedorFavoritos) {
        contenedorFavoritos.addEventListener('click', async function(e) {
            // Buscamos si el clic fue en el botón de eliminar o en su ícono
            const btnEliminar = e.target.closest('.btn-eliminar-fav');

            if (btnEliminar) {
                const idRecurso = btnEliminar.getAttribute('data-id');
                
                if (!confirm('¿Estás seguro de que deseas quitar este recurso de tus favoritos?')) {
                    return;
                }

                // Efecto visual inmediato (opcional: opacidad mientras carga)
                const itemCard = document.getElementById(`fav-item-${idRecurso}`);
                if(itemCard) itemCard.style.opacity = '0.5';

                try {
                    const token = window.csrfToken || '';
                    const datos = {
                        accion: 'marcarFavorito',
                        id_recurso: idRecurso,
                        es_favorito: false // False para eliminar
                    };

                    const respuesta = await fetch('../../controllers/AuthController.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': token },
                        body: JSON.stringify(datos)
                    });

                    const resultado = await respuesta.json();

                    if (resultado.exito) {
                        // Si se borró bien, eliminamos el elemento del HTML suavemente
                        if (itemCard) {
                            itemCard.remove();
                        }
                        // Si ya no quedan hijos, mostramos mensaje de vacío
                        if (contenedorFavoritos.children.length === 0) {
                            contenedorFavoritos.innerHTML = '<p style="text-align:center;">No tienes favoritos guardados.</p>';
                        }
                        mostrarMensajeModal('Favorito eliminado', 'exito'); // Usamos tu modal existente
                        setTimeout(() => { 
                             const msg = document.getElementById('mensaje-perfil');
                             if(msg) msg.style.display = 'none'; 
                        }, 2000);
                    } else {
                        mostrarMensajeModal('Error al eliminar', 'error');
                        if(itemCard) itemCard.style.opacity = '1'; // Revertir opacidad
                    }

                } catch (error) {
                    console.error(error);
                    mostrarMensajeModal('Error de conexión', 'error');
                    if(itemCard) itemCard.style.opacity = '1';
                }
            }
        });
    }

    // Evento del formulario de actualización
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            if (inputPass1 && inputPass2 && inputPass1.value !== inputPass2.value) {
                mostrarMensajeModal('Las contraseñas no coinciden.', 'error');
                return;
            }
            
            mostrarMensajeModal('Actualizando...', 'info');

            const datos = {
                accion: 'actualizarUsuario',
                id_usuario: inputId ? inputId.value : '',
                nombre: inputNombreForm ? inputNombreForm.value : '',
                apellidos: inputApellidosForm ? inputApellidosForm.value : '',
                correo: inputCorreoForm ? inputCorreoForm.value : '',
                telefono: inputTelefonoForm ? inputTelefonoForm.value : '',
                contrasena: inputPass1 ? inputPass1.value : ''
            };

            try {
                const token = window.csrfToken || '';
                const respuesta = await fetch('../../controllers/AuthController.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': token },
                    body: JSON.stringify(datos)
                });
                
                const resultado = await respuesta.json();
                
                if (resultado.exito) {
                    mostrarMensajeModal('¡Actualizado con éxito!', 'exito');
                    
                    const usuarioActualizado = { 
                        ...JSON.parse(localStorage.getItem('usuarioActual')), 
                        nombre: datos.nombre,
                        apellidos: datos.apellidos,
                        correo: datos.correo,
                        telefono: datos.telefono
                    };
                    localStorage.setItem('usuarioActual', JSON.stringify(usuarioActualizado));
                    
                    cargarDatosUsuario();
                    if (modal) {
                        setTimeout(() => { 
                            modal.style.display = 'none'; 
                            mostrarMensajeModal('', 'info', false); 
                        }, 2000);
                    }
                } else {
                    mostrarMensajeModal('Error: ' + resultado.mensaje, 'error');
                }
            } catch (error) {
                mostrarMensajeModal('Error de conexión.', 'error');
            }
        });
    }

    // Listeners Modal
    if (btnAbrirModal && modal) {
        btnAbrirModal.addEventListener('click', () => modal.style.display = 'flex');
    }
    if (btnCerrarModal && modal) {
        btnCerrarModal.addEventListener('click', () => modal.style.display = 'none');
    }
    if (modal) {
        modal.addEventListener('click', (e) => { 
            if (e.target === modal) modal.style.display = 'none'; 
        });
    }

    function mostrarMensajeModal(texto, tipo, mostrar = true) {
        if (mensajePerfil) {
            mensajePerfil.textContent = texto;
            mensajePerfil.className = `mensajeGeneral ${tipo}`;
            mensajePerfil.style.display = mostrar ? 'block' : 'none';
        }
    }

    // Inicializar funcionalidad del perfil administrador
    cargarDatosUsuario();
    cargarFavoritos();
}