// perfil.js - Manejo de perfiles específicos por tipo de usuario

document.addEventListener('DOMContentLoaded', function() {
    // Obtener datos del usuario desde localStorage
    const usuarioActualStorage = localStorage.getItem('usuarioActual');
    let datosUsuario = null;
    
    // Configurar nombre de bienvenida
    const nombreBienvenida = document.getElementById('nombreBienvenida');
    if (usuarioActualStorage && nombreBienvenida) {
        try {
            datosUsuario = JSON.parse(usuarioActualStorage);
            const nombre = datosUsuario.nombre.trim();
            nombreBienvenida.textContent = `Mi perfil ${nombre}`;
        } catch (e) {
            console.warn('usuarioActual corrupto en localStorage');
        }
    }

    // Si no tenemos datos del usuario, no podemos continuar
    if (!datosUsuario) return;

    const id = datosUsuario.id;
    const tipoUsuario = datosUsuario.tipoUsuario;

    // Configurar navegación específica según tipo de usuario
    configurarNavegacion(tipoUsuario, id);

    // Configurar cerrar sesión
    configurarCerrarSesion();

    // Configurar enlaces de perfil
    configurarEnlacesPerfil(tipoUsuario);
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
                <li><a href="#">Notificaciones</a></li>
                <li><a href="perfilAlumno.php">Mi cuenta</a></li>
                <li><a href="#" class="lnk" id="btnCerrarSesion">Cerrar Sesión</a></li>
            `;
        } else {
            navListAlumno.innerHTML = `
                <li><a href="alumno.php" id="unirmeGrupo">Unirme a grupo</a></li>
                <li><a href="panelGestionContenidoAlumno.php">Subir Contenido</a></li>
                <li><a href="#">Notificaciones</a></li>
                <li><a href="perfilAlumno.php">Mi cuenta</a></li>
                <li><a href="#" class="lnk" id="btnCerrarSesion">Cerrar Sesión</a></li>
            `;
        }
        // Reconfigurar eventos después de actualizar el HTML
        configurarCerrarSesion();
        configurarEnlacesPerfil('alumno');
    }
}

// Función para configurar enlaces de perfil que redirijan según el tipo de usuario
function configurarEnlacesPerfil(tipoUsuario) {
    // Buscar todos los enlaces que apunten a perfil
    const enlacesPerfil = document.querySelectorAll('a[href="#"]');
    
    enlacesPerfil.forEach(enlace => {
        if (enlace.textContent.includes('Mi cuenta') || enlace.textContent.includes('perfil')) {
            enlace.addEventListener('click', function(e) {
                e.preventDefault();
                redirigirAPerfil(tipoUsuario);
            });
        }
    });
}

// Función para redirigir al perfil correcto según el tipo de usuario
function redirigirAPerfil(tipoUsuario) {
    switch (tipoUsuario) {
        case 'administrador':
            window.location.href = 'perfilAdministrador.php';
            break;
        case 'docente':
            window.location.href = 'perfilDocente.php';
            break;
        case 'alumno':
            window.location.href = 'perfilAlumno.php';
            break;
        default:
            window.location.href = 'perfil.php'; // Fallback
    }
}

// Función para configurar el cerrar sesión con validaciones completas
function configurarCerrarSesion() {
    const btnCerrarSesion = document.getElementById('btnCerrarSesion');
    if (btnCerrarSesion) {
        // Remover event listeners previos clonando el elemento
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
        try { localStorage.clear(); } catch(e){}
        window.location.href = '../../../index.php';
    }
}