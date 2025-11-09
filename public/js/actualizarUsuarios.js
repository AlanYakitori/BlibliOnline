document.addEventListener('DOMContentLoaded', function() {
    
    const formulario = document.getElementById('formularioRegistro');
    const token = window.csrfToken; 

    cargarDatosUsuario();
    
    formulario.addEventListener('submit', guardarCambios);

    async function cargarDatosUsuario() {
        const urlParams = new URLSearchParams(window.location.search);
        const idUsuario = urlParams.get('id');

        if (!idUsuario) {
            alert('Error: No se especificó un ID de usuario.');
            window.location.href = 'PanelGestionAdministrador.php'; 
            return;
        }

        try {
            const respuesta = await fetch('../../controllers/AuthController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': token },
                body: JSON.stringify({
                    accion: 'consultarUsuarioUnico', 
                    id_usuario: idUsuario
                })
            });

            const resultado = await respuesta.json();

            if (resultado.exito && resultado.usuario) {
                rellenarFormulario(resultado.usuario);
                formulario.disabled = false; 
            } else {
                alert('Error: ' + (resultado.mensaje || 'No se pudo cargar el usuario.'));
            }
        } catch (error) {
            console.error('Error fetch (cargar):', error);
            alert('Error: No se pudo cargar la información del usuario.');
        }
    }

    function rellenarFormulario(usuario) {
        document.getElementById('nombreCompleto').value = usuario.nombre;
        document.getElementById('apellidosCompletos').value = usuario.apellidos;
        document.getElementById('telefonoContacto').value = usuario.telefono;
        document.getElementById('cargoAdministrativo').value = usuario.dato; // Asumo que cargo = dato
        document.getElementById('sexo').value = usuario.genero;
        document.getElementById('fechaNacimiento').value = usuario.fechaNacimiento;
        document.getElementById('correoElectronico').value = usuario.correo;
    }

    async function guardarCambios(evento) {
        evento.preventDefault(); // Evita recargar
        
        const urlParams = new URLSearchParams(window.location.search);
        const idUsuario = urlParams.get('id');
        if (!idUsuario) {
             alert('Error fatal: El ID del usuario se ha perdido.');
             return;
        }

        const contrasena = document.getElementById('contrasena').value;
        const confirmarContrasena = document.getElementById('confirmarContrasena').value;
        
        if (contrasena !== confirmarContrasena) {
            alert('Error: Las nuevas contraseñas no coinciden.');
            return;
        }
        
        const datosActualizados = {
            id_usuario: idUsuario,
            accion: 'actualizarUsuario', 
            nombre: document.getElementById('nombreCompleto').value,
            apellidos: document.getElementById('apellidosCompletos').value,
            telefono: document.getElementById('telefonoContacto').value,
            dato: document.getElementById('cargoAdministrativo').value,
            genero: document.getElementById('sexo').value,
            fechaNacimiento: document.getElementById('fechaNacimiento').value,
            correo: document.getElementById('correoElectronico').value,
            tipoUsuario: 'administrador' 
        };

        if (contrasena.length > 0) {
            if (contrasena.length < 8) {
                 alert('Error: La nueva contraseña debe tener al menos 8 caracteres.');
                 return;
            }
            datosActualizados.contrasena = contrasena; 
        }
        try {
            const respuesta = await fetch('../../controllers/AuthController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': token },
                body: JSON.stringify(datosActualizados)
            });

            const resultado = await respuesta.json();

            

            if (resultado.exito) {
                alert(resultado.mensaje || 'Datos actualizados con éxito.');
                window.location.href = 'consultarAdministrador.php';
            } else {
                alert('Error al actualizar: ' + (resultado.mensaje || 'Datos no válidos.'));
            }

        } catch (error) {
            console.error('Error fetch (guardar):', error);
            alert('Error de conexión al guardar los cambios.');
        }
    }
});