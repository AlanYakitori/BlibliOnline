document.addEventListener('DOMContentLoaded', function() {
    
    console.log('Hola mundo');

    consultarUsuarios();

    async function consultarUsuarios() {
        try {
            const token = window.csrfToken; 

            const respuesta = await fetch('../../controllers/AuthController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': token
                },
                body: JSON.stringify({
                    accion: 'consultarUsuarios' 
                })
            });
            
            const resultado = await respuesta.json(); // Convierte la respuesta a objeto JS
            
            if (resultado.exito && resultado.usuarios) {
                pintarTabla(resultado.usuarios); 
            } else {
                mostrarErrorTabla(resultado.mensaje || 'No se pudieron cargar los datos.');
            }
            
        } catch (error) {
            console.error('Error en fetch:', error);
            mostrarErrorTabla('Error de conexión con el servidor.');
        }
    }

    /**
     * Dibuja las filas de la tabla en el <tbody>
     * @param {array} usuarios - El array de usuarios que vino del servidor.
     */
    function pintarTabla(usuarios) {
        const cuerpoTabla = document.getElementById("tabla-usuarios-body");
        
        cuerpoTabla.innerHTML = ""; 
        
        if (usuarios.length === 0) {
            cuerpoTabla.innerHTML = '<tr><td colspan="8" class="text-center">No se encontraron usuarios.</td></tr>';
            return;
        }

        let filasHtml = "";

        usuarios.forEach(usuario => {
            const estadoBadge = usuario.aceptado == 1
                ? '<span class="badge bg-success">Aceptado</span>' 
                : '<span class="badge bg-warning">Pendiente</span>';

            filasHtml += `
                <tr data-id-usuario="${usuario.id_usuario}">
                    <td>${usuario.id_usuario}</td>
                    <td>${usuario.nombre}</td>
                    <td>${usuario.apellidos}</td>
                    <td>${usuario.correo}</td>
                    <td>${usuario.telefono}</td>
                    <td>${usuario.tipoUsuario}</td>
                    <td>${estadoBadge}</td>
                    <td>
                        <a href="actualizarAdministrador.php?id=${usuario.id_usuario}" class="btnActualizar">Editar</a>
                        <a class="btnEliminar" onclick="confirmarEliminar(${usuario.id_usuario})">Eliminar</a>
                    </td>
                </tr>
            `;
        });

        cuerpoTabla.innerHTML = filasHtml;
    }

    function mostrarErrorTabla(mensaje) {
        const cuerpoTabla = document.getElementById("tabla-usuarios-body");
        cuerpoTabla.innerHTML = `<tr><td colspan="8" class="text-center text-danger">${mensaje}</td></tr>`;
    }
});

    async function confirmarEliminar(id) {
        if (confirm(`¿Estás seguro de que quieres eliminar al usuario con ID ${id}?`)) {
            try {
                const token = window.csrfToken; 

                const respuesta = await fetch('../../controllers/AuthController.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': token
                    },
                    body: JSON.stringify({
                        accion: 'eliminarUsuario', 
                        id_usuario: id           
                    })
                });
                const resultado = await respuesta.json();
                if (resultado.exito) {
                    alert(resultado.mensaje || 'Usuario eliminado correctamente');
                    
                    const fila = document.querySelector(`tr[data-id-usuario="${id}"]`);
                    
                    if (fila) {
                        fila.remove(); 
                    }
                } else {
                    alert('Error al eliminar: ' + (resultado.mensaje || 'Error desconocido'));
                }
            } catch (error) {
                console.error('Error en fetch:', error);
            }
            console.log('Eliminando usuario ID:', id);
        }
    }