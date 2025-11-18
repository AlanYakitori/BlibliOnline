document.addEventListener('DOMContentLoaded', () => {

    // Referencias del Modal y Formulario
    const form = document.getElementById('form-actualizar-datos');
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
    const btnCerrarModal = modal.querySelector('.modal-cerrar');

    
    // 1. Cargar Datos
    function cargarDatosUsuario() {
        const usuarioStorage = localStorage.getItem('usuarioActual');
        if (!usuarioStorage) return;
        
        try {
            const usuario = JSON.parse(usuarioStorage);
            vistaNombre.textContent = usuario.nombre;
            vistaApellidos.textContent = usuario.apellidos;
            vistaCorreo.textContent = usuario.correo;
            vistaTelefono.textContent = usuario.telefono || 'No registrado';
            
            inputId.value = usuario.id;
            inputNombreForm.value = usuario.nombre;
            inputApellidosForm.value = usuario.apellidos;
            inputCorreoForm.value = usuario.correo;
            inputTelefonoForm.value = usuario.telefono || '';
        } catch (e) { console.error('Error LS', e); }
    }

    // 2. Cargar Favoritos
    async function cargarFavoritos() {
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
        contenedorFavoritos.innerHTML = ''; 
        
        if (favoritos.length === 0) {
            contenedorFavoritos.innerHTML = '<p style="text-align:center;">No tienes favoritos guardados.</p>';
            return;
        }

        favoritos.forEach(fav => {
            const urlLimpia = fav.archivo_url || '#';
            const urlTexto = urlLimpia.length > 40 ? urlLimpia.substring(0, 40) + '...' : urlLimpia;

            const itemHtml = `
                <div class="item-favorito">
                    <div class="info-favorito">
                        <h3>${fav.titulo}</h3>
                        <small title="${urlLimpia}">
                            <i class="fa-solid fa-link"></i> ${urlTexto}
                        </small>
                    </div>
                    <a href="${urlLimpia}" target="_blank" class="btn-ir-recurso" title="Ir al recurso">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                </div>
            `;
            contenedorFavoritos.innerHTML += itemHtml;
        });
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        if (inputPass1.value !== inputPass2.value) {
            mostrarMensajeModal('Las contraseñas no coinciden.', 'error');
            return;
        }
        
        mostrarMensajeModal('Actualizando...', 'info');

        const datos = {
            accion: 'actualizarUsuario',
            id_usuario: inputId.value,
            nombre: inputNombreForm.value,
            apellidos: inputApellidosForm.value,
            correo: inputCorreoForm.value,
            telefono: inputTelefonoForm.value,
            contrasena: inputPass1.value 
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
                setTimeout(() => { modal.style.display = 'none'; mostrarMensajeModal('', 'info', false); }, 2000);
            } else {
                mostrarMensajeModal('Error: ' + resultado.mensaje, 'error');
            }
        } catch (error) {
            mostrarMensajeModal('Error de conexión.', 'error');
        }
    });

    // Listeners Modal
    btnAbrirModal.addEventListener('click', () => modal.style.display = 'flex');
    btnCerrarModal.addEventListener('click', () => modal.style.display = 'none');
    modal.addEventListener('click', (e) => { if (e.target === modal) modal.style.display = 'none'; });

    function mostrarMensajeModal(texto, tipo, mostrar = true) {
        mensajePerfil.textContent = texto;
        mensajePerfil.className = `mensajeGeneral ${tipo}`;
        mensajePerfil.style.display = mostrar ? 'block' : 'none';
    }

    cargarDatosUsuario();
    cargarFavoritos();
});