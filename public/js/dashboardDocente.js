document.addEventListener('DOMContentLoaded', function() {
    const usuarioActualStorage = localStorage.getItem('usuarioActual');
    const nombreBienvenida = document.getElementById('nombreBienvenida');

    if (usuarioActualStorage) {
        try { 
            console.log('Usuario actual:', JSON.parse(usuarioActualStorage)); 
            
            const nombre = datosUsuario.nombre.trim();
            nombreBienvenida.textContent = `Bienvenido ${nombre}`;
        } catch(e){
            
        }
    }

    const btnCerrarSesion = document.getElementById('btnCerrarSesion');
    
    if (btnCerrarSesion) btnCerrarSesion.addEventListener('click', cerrarSesion);

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

    window.history.pushState(null, '', window.location.href);
    window.onpopstate = function () { window.history.pushState(null, '', window.location.href); };
});