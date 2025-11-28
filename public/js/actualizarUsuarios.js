document.addEventListener('DOMContentLoaded', function() {
    
    const formulario = document.getElementById('formularioRegistro');
    const token = window.csrfToken; 
    const mensajeFeedback = document.getElementById('mensaje-feedback');

    // Determina el rol del usuario que se está actualizando (basado en la URL)
    const url = window.location.pathname;
    let rolActualizado = '';

    if (url.includes('actualizarAdministrador')) {
        rolActualizado = 'administrador';
    } else if (url.includes('actualizarDocente')) {
        rolActualizado = 'docente';
    } else if (url.includes('actualizarAlumno')) {
        rolActualizado = 'alumno';
    }

    // Inicializa la carga de datos
    cargarDatosUsuario();
    
    // Asigna el listener al formulario
    if (formulario) {
        formulario.addEventListener('submit', guardarCambios);
    }

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
                // Rellena el formulario
                rellenarFormulario(resultado.usuario, rolActualizado);
                // Habilita el formulario
                formulario.disabled = false; 
            } else {
                alert('Error: ' + (resultado.mensaje || 'No se pudo cargar el usuario.'));
            }
        } catch (error) {
            console.error('Error fetch (cargar):', error);
            alert('Error de conexión al cargar la información.');
        }
    }

    // --- FUNCIÓN PARA RELLENAR EL FORMULARIO ---
    function rellenarFormulario(usuario, rol) {
        // Campos comunes
        document.getElementById('nombreCompleto').value = usuario.nombre;
        document.getElementById('apellidosCompletos').value = usuario.apellidos;
        document.getElementById('telefonoContacto').value = usuario.telefono;
        document.getElementById('sexo').value = usuario.genero;
        document.getElementById('fechaNacimiento').value = usuario.fechaNacimiento;
        document.getElementById('correoElectronico').value = usuario.correo;

        // Campo 'DATO' que cambia por rol
        if (rol === 'administrador') {
            document.getElementById('cargoAdministrativo').value = usuario.dato;
        } else if (rol === 'docente') {
            document.getElementById('especialidadDocente').value = usuario.dato;
        } else if (rol === 'alumno') {
            document.getElementById('matriculaAlumno').value = usuario.dato;
        }
    }

    // --- FUNCIÓN PARA GUARDAR CAMBIOS ---
    async function guardarCambios(evento) {
        evento.preventDefault(); 
        
        const urlParams = new URLSearchParams(window.location.search);
        const idUsuario = urlParams.get('id');
        
        // Contraseñas
        const contrasena = document.getElementById('contrasena').value;
        const confirmarContrasena = document.getElementById('confirmarContrasena').value;
        
        if (contrasena !== confirmarContrasena) {
            alert('Error: Las nuevas contraseñas no coinciden.');
            return;
        }
        
        // Mapeo del campo 'DATO'
        let valorDato = '';
        if (rolActualizado === 'administrador') {
            valorDato = document.getElementById('cargoAdministrativo').value;
        } else if (rolActualizado === 'docente') {
            valorDato = document.getElementById('especialidadDocente').value;
        } else if (rolActualizado === 'alumno') {
            valorDato = document.getElementById('matriculaAlumno').value;
        }

        const datosActualizados = {
            id_usuario: idUsuario,
            accion: 'actualizarUsuario', 
            nombre: document.getElementById('nombreCompleto').value,
            apellidos: document.getElementById('apellidosCompletos').value,
            telefono: document.getElementById('telefonoContacto').value,
            dato: valorDato, // El campo dinámico
            genero: document.getElementById('sexo').value,
            fechaNacimiento: document.getElementById('fechaNacimiento').value,
            correo: document.getElementById('correoElectronico').value,
            tipoUsuario: rolActualizado // El rol que se está actualizando
        };

        // Lógica de contraseña opcional
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
                // Redirigir a la vista de gestión o donde corresponda
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