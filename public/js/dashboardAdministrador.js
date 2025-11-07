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

    const btnCerrarSesion = document.getElementById('btnCerrarSesion');
    if (btnCerrarSesion) {
        btnCerrarSesion.addEventListener('click', function() {
            cerrarSesion();
        });
    }

    const btnBackup = document.getElementById('btnBackup');
    if (btnBackup) {
        btnBackup.addEventListener('click', function() {
            crearBackup();
        });
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

    async function crearBackup() {
        try {
            if (!confirm('¿Deseas generar una copia de seguridad de la base de datos?')) return;

            accion = 'crearBackup';

            const payload = { accion: accion };

            const respuesta = await fetch('../../controllers/DataBaseController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const resultado = await respuesta.json();
            if (!resultado.exito) {
                alert('Error al generar backup: ' + (resultado.mensaje || ''));
                return;
            }

            // Construir la URL de descarga usando la ruta conocida al controlador
            const serverFile = resultado.file; // e.g. copiaSeguridad_20251106_123456.bak
            const downloadUrl = `../../controllers/DataBaseController.php?download=${encodeURIComponent(serverFile)}`;

            // Forzar la descarga navegando a la URL (el controlador enviará headers de attachment)
            window.location.href = downloadUrl;

        } catch (err) {
            console.error('Error creando backup:', err);
            alert('Ocurrió un error al generar la copia de seguridad. Revisa la consola.');
        }
    }

    // Evitar mostrar contenido vía "back" (segunda capa)
    window.history.pushState(null, '', window.location.href);
    window.onpopstate = function () { window.history.pushState(null, '', window.location.href); };
});
