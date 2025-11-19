document.addEventListener('DOMContentLoaded', function() {
    const usuarioActualStorage = localStorage.getItem('usuarioActual');
    const nombreBienvenida = document.getElementById('nombreBienvenida');
    
    if (usuarioActualStorage && nombreBienvenida) { 
        try {
            const datosUsuario = JSON.parse(usuarioActualStorage);
            const nombre = datosUsuario.nombre.trim();
            nombreBienvenida.textContent = `¡Hola ${nombre}!`;
        } catch (e) {
            console.warn('LocalStorage corrupto o nombre no encontrado'); 
        }
    }

    const btnCerrarSesion = document.getElementById('btnCerrarSesion');
    if (btnCerrarSesion) {
        btnCerrarSesion.addEventListener('click', function(e) { 
            e.preventDefault(); 
            cerrarSesion();
        });
    }

    const btnBackup = document.getElementById('btnBackup');
    if (btnBackup) {
        btnBackup.addEventListener('click', function() {
            crearBackup();
        });
    }

    const formRestaurar = document.getElementById('formularioDB');
    if (formRestaurar) {
        formRestaurar.addEventListener('submit', function(e) {
            e.preventDefault();
            procesarRestauracion();
        });
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

    async function crearBackup() {
        try {
            if (!confirm('¿Deseas generar una copia de seguridad?')) return;

            const accion = 'crearBackup'; 
            const payload = { accion: accion };

            const respuesta = await fetch('../../controllers/DataBaseController.php', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': window.csrfToken || '' 
                },
                body: JSON.stringify(payload)
            });

            const resultado = await respuesta.json();
            if (!resultado.exito) {
                alert('Error al generar backup: ' + (resultado.mensaje || ''));
                return;
            }

            const serverFile = resultado.file;
            const downloadUrl = `../../controllers/DataBaseController.php?download=${encodeURIComponent(serverFile)}`;
            window.location.href = downloadUrl;

        } catch (err) {
            console.error('Error creando backup:', err);
        }
    }

    async function procesarArchivoRestauracion(archivo) {
        try {
            if (!archivo.name.toLowerCase().endsWith('.bak')) {
                alert('Solo se permiten archivos .bak');
                return;
            }
            if (!confirm('¿Desea restaurar la base de datos?')) return;

            const formData = new FormData();
            formData.append('accion', 'restaurarBackup');
            formData.append('backupFile', archivo);

            alert('Restaurando, por favor espere...');

            const respuesta = await fetch('../../controllers/DataBaseController.php', {
                method: 'POST',
                headers: {
                    'X-CSRF-Token': window.csrfToken || '' // Header añadido
                },
                body: formData
            });

            const resultado = await respuesta.json();

            if (resultado.exito) {
                alert('✅ ' + resultado.mensaje);
                if (confirm('¿Desea recargar la página?')) {
                    window.location.reload();
                }
            } else {
                alert('❌ Error: ' + resultado.mensaje);
            }
        } catch (err) {
            console.error('Error procesando restauración:', err);
        }
    }

    async function procesarRestauracion() {
        const fileInput = document.getElementById('inputArchivoDB');
        if (!fileInput || !fileInput.files[0]) {
            alert('Es necesario seleccionar un archivo .bak');
            return;
        }
        await procesarArchivoRestauracion(fileInput.files[0]);
    }
    
});