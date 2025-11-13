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

    // Manejar formulario de restauración si existe
    const formRestaurar = document.getElementById('formularioDB');
    if (formRestaurar) {
        formRestaurar.addEventListener('submit', function(e) {
            e.preventDefault();
            procesarRestauracion();
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

    async function procesarArchivoRestauracion(archivo) {
        try {
            // Validar extensión
            if (!archivo.name.toLowerCase().endsWith('.bak')) {
                alert('Solo se permiten archivos .bak');
                return;
            }

            // Confirmar antes de restaurar
            if (!confirm('¿Desea restaurar los datos del sistema? Esta acción puede sobrescribir datos actuales.')) {
                return;
            }

            // Crear FormData para enviar el archivo
            const formData = new FormData();
            formData.append('accion', 'restaurarBackup');
            formData.append('backupFile', archivo);

            // Mostrar mensaje de progreso
            alert('Restaurando base de datos, por favor espere...');

            const respuesta = await fetch('../../controllers/DataBaseController.php', {
                method: 'POST',
                body: formData
            });

            const resultado = await respuesta.json();

            if (resultado.exito) {
                alert('✅ ' + resultado.mensaje);
                // Opcional: recargar la página después de restauración exitosa
                if (confirm('¿Desea recargar la página para reflejar los cambios?')) {
                    window.location.reload();
                }
            } else {
                alert('❌ Error: ' + resultado.mensaje);
            }

        } catch (err) {
            console.error('Error procesando restauración:', err);
            alert('❌ Ocurrió un error durante la restauración. Revisa la consola.');
        }
    }

    async function procesarRestauracion() {
        // Función para manejar el formulario HTML
        const fileInput = document.getElementById('inputArchivoDB');
        
        if (!fileInput || !fileInput.files[0]) {
            alert('Es necesario seleccionar un archivo .bak para la restauración');
            return;
        }
        
        await procesarArchivoRestauracion(fileInput.files[0]);
    }

    // Evitar mostrar contenido vía "back" (segunda capa)
    window.history.pushState(null, '', window.location.href);
    window.onpopstate = function () { window.history.pushState(null, '', window.location.href); };
});
