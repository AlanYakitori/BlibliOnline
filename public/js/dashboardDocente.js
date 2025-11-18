document.addEventListener('DOMContentLoaded', function() {
    const usuarioActualStorage = localStorage.getItem('usuarioActual');
    const nombreBienvenida = document.getElementById('nombreBienvenida');

    if (usuarioActualStorage) {
        try { 
            const datosUsuario = JSON.parse(usuarioActualStorage);

            const nombre = datosUsuario.nombre.trim();
            nombreBienvenida.textContent = `Bienvenido ${nombre}`;
        } catch(e){
            console.warn('usuarioActual corrupto en localStorage');  
        }
    }

    const datosUsuario = JSON.parse(usuarioActualStorage);
    const id = datosUsuario.id;

    const btnCerrarSesion = document.getElementById('btnCerrarSesion');
    if (btnCerrarSesion) {
        btnCerrarSesion.addEventListener('click', function() {
            cerrarSesion();
        });
    }

    const btnVerGrupos = document.getElementById('btnGrupos');
    if (btnVerGrupos) {
        console.log('Botón de Ver Grupos encontrado');
        btnVerGrupos.addEventListener('click', function() {
            console.log('Botón Ver Grupos clickeado');
            verGrupos();
        });
    } else {
        console.error('No se encontró el botón con id btnGrupos');
    }

    const formCrearGrupo = document.getElementById('formularioGrupo');
    if (formCrearGrupo) {
        formCrearGrupo.addEventListener('submit', function(e) {
            e.preventDefault();
            crearGrupo();
        });
    }

    const btnReporteGrupos = document.getElementById('btnReporteGrupos');
    if (btnReporteGrupos) {
        btnReporteGrupos.addEventListener('click', function() {
            generarReporteGrupos();
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

    async function verGrupos() {
        console.log('Función verGrupos iniciada');
        let accion = 'verGrupos';
        function obtenerDatos() {
            return {
                idDocente: id,
                accion: accion
            };
        }
        datos = obtenerDatos();
        console.log('Datos a enviar:', datos);
        
        try {
            const respuesta = await fetch('../../controllers/GrupoController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(datos)
            });
            
            console.log('Respuesta recibida:', respuesta);
            const resultado = await respuesta.json();
            console.log('Resultado parseado:', resultado);
            
            if (resultado.exito) {
                mostrarGrupos(resultado.grupos);
            } else {
                console.error('Error al obtener grupos:', resultado.mensaje);
                alert('Error al cargar los grupos: ' + resultado.mensaje);
            }
        } catch (error) {
            console.error('Error en la petición:', error);
            alert('Error al conectar con el servidor: ' + error.message);
        }
    }

    function mostrarGrupos(grupos) {
        console.log('Función mostrarGrupos iniciada con:', grupos);
        
        // Buscar contenedor para mostrar los grupos
        let contenedorGrupos = document.getElementById('contenedorGrupos');
        if (!contenedorGrupos) {
            console.error('No se encontró el contenedor de grupos');
            return;
        }
        
        console.log('Contenedor encontrado:', contenedorGrupos);

        // Limpiar contenido anterior
        contenedorGrupos.innerHTML = '';

        if (grupos.length === 0) {
            console.log('No hay grupos para mostrar');
            contenedorGrupos.innerHTML = '<p style="color: #999; font-style: italic; text-align: center;">No tienes grupos creados.</p>';
            return;
        }

        console.log(`Mostrando ${grupos.length} grupos`);
        
        // Crear HTML para cada grupo
        grupos.forEach(grupo => {
            console.log('Procesando grupo:', grupo);
            const grupoDiv = document.createElement('div');
            grupoDiv.className = 'contenido-tarjeta grupo-tarjeta';
            grupoDiv.innerHTML = `
                <div class="contenido-header">
                    <h3 class="contenido-titulo">${grupo.nombre}</h3>
                    <div class="contenido-acciones">
                        <button class="btn-actualizar" onclick="actualizarGrupo(${grupo.id_grupo}, '${grupo.nombre}', '${grupo.clave}')">Actualizar</button>
                        <button class="btn-eliminar" onclick="eliminarGrupo(${grupo.id_grupo})">Eliminar</button>
                    </div>
                </div>
                
                <div class="contenido-info">
                    <div class="contenido-descripcion"><strong>Código de acceso:</strong> ${grupo.clave}</div>
                    <div class="contenido-categoria"><strong>Total miembros:</strong> ${grupo.total_miembros}</div>
                </div>
                
                <div class="miembros-lista">
                    <h4>Miembros del grupo:</h4>
                    ${grupo.miembros.length > 0 ? 
                        grupo.miembros.map(miembro => `
                            <div class="miembro-item">
                                <div class="miembro-info">
                                    <span class="miembro-nombre">${miembro.nombre} ${miembro.apellidos}</span>
                                    <span class="miembro-email">(${miembro.correo})</span>
                                </div>
                                <div class="miembro-acciones">
                                    <button class="btn-eliminar btn-eliminar-small" onclick="eliminarMiembro(${miembro.id_miembro_grupo}, '${miembro.nombre}')">Eliminar</button>
                                </div>
                            </div>
                        `).join('') 
                        : '<p class="sin-miembros">No hay miembros en este grupo.</p>'
                    }
                </div>
            `;
            contenedorGrupos.appendChild(grupoDiv);
        });
        
        console.log('Grupos mostrados exitosamente');
    }

    // Función para mostrar mensaje general
    function mostrarMensaje(texto, tipo) {
        const mensajeAnterior = document.querySelector('.mensajeGeneral');
        if (mensajeAnterior) mensajeAnterior.remove();

        const mensaje = document.createElement('div');
        mensaje.className = `mensajeGeneral ${tipo}`;
        mensaje.textContent = texto;

        document.body.appendChild(mensaje);

        setTimeout(() => {
            if (mensaje.parentNode) mensaje.remove();
        }, 5000);
    }

    async function crearGrupo() {
        let accion = 'crearGrupo';
        const nombre = document.getElementById('inputNombreGrupo').value.trim();
        
        // Validar que el nombre no esté vacío
        if (!nombre) {
            mostrarMensaje('El nombre del grupo es obligatorio', 'error');
            return;
        }

        // Generar un codigo de clase automatico de 8 caracteres
        const clave = Array.from({length: 8}, () => String.fromCharCode(65 + Math.floor(Math.random() * 26))).join('');

        function obtenerDatos(){
            return{
                docente: id,
                clave: clave,
                nombre: nombre,
                accion: accion
            };
        }

        datos = obtenerDatos();

        try {
            mostrarMensaje('Creando grupo...', 'info');
            
            const respuesta = await fetch('../../controllers/GrupoController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(datos)
            });
            
            console.log('Respuesta recibida:', respuesta);
            const resultado = await respuesta.json();
            console.log('Resultado parseado:', resultado);
            
            if (resultado.exito) {
                mostrarMensaje('¡Grupo creado exitosamente!', 'exito');
                
                // Limpiar el formulario
                document.getElementById('inputNombreGrupo').value = '';
                
                // Refrescar la página después de 5 segundos
                setTimeout(() => {
                    window.location.reload();
                }, 5000);
            } else {
                mostrarMensaje(resultado.mensaje || 'Error al crear el grupo', 'error');
            }
        } catch (error) {
            console.error('Error en la petición:', error);
            mostrarMensaje('Error al conectar con el servidor', 'error');
        }
    }

    // Función para mostrar formulario de actualización
    window.actualizarGrupo = function(idGrupo, nombreActual, claveActual) {
        const contenedorActualizar = document.getElementById('contenedorActualizarGrupos');
        
        if (!contenedorActualizar) {
            mostrarMensaje('Error: No se encontró el contenedor de actualización', 'error');
            return;
        }

        // Crear el formulario de actualización
        contenedorActualizar.innerHTML = `
            <div class="formulario-actualizar-grupo">
                <h3>Actualizar Grupo</h3>
                <form id="formularioActualizarGrupo" class="formularioGrupo">
                    <label for="inputNuevoNombreGrupo">Nombre del grupo:</label>
                    <input type="text" id="inputNuevoNombreGrupo" value="${nombreActual}" required>
                    <input type="hidden" id="idGrupoActualizar" value="${idGrupo}">
                    <div class="botones-formulario">
                        <input type="submit" value="Actualizar Grupo" class="btnGenerar">
                        <button type="button" onclick="cancelarActualizacion()" class="btnCancelar">Cancelar</button>
                    </div>
                </form>
            </div>
        `;

        // Scroll hasta el formulario
        contenedorActualizar.scrollIntoView({ behavior: 'smooth' });

        // Agregar event listener al formulario
        const formularioActualizar = document.getElementById('formularioActualizarGrupo');
        formularioActualizar.addEventListener('submit', function(e) {
            e.preventDefault();
            enviarActualizacionGrupo();
        });
    }

    // Función para cancelar la actualización
    window.cancelarActualizacion = function() {
        const contenedorActualizar = document.getElementById('contenedorActualizarGrupos');
        contenedorActualizar.innerHTML = '';
    }

    // Función para enviar la actualización del grupo
    async function enviarActualizacionGrupo() {
        const idGrupo = document.getElementById('idGrupoActualizar').value;
        const nuevoNombre = document.getElementById('inputNuevoNombreGrupo').value.trim();

        // Validar que el nombre no esté vacío
        if (!nuevoNombre) {
            mostrarMensaje('El nombre del grupo es obligatorio', 'error');
            return;
        }

        try {
            mostrarMensaje('Actualizando grupo...', 'info');
            
            const datos = {
                accion: 'actualizarGrupo',
                idGrupo: idGrupo,
                nuevoNombre: nuevoNombre
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
                mostrarMensaje('¡Grupo actualizado exitosamente!', 'exito');
                
                // Limpiar el formulario de actualización
                cancelarActualizacion();
                
                // Refrescar la vista de grupos después de 2 segundos
                setTimeout(() => {
                    verGrupos();
                }, 2000);
            } else {
                mostrarMensaje(resultado.mensaje || 'Error al actualizar el grupo', 'error');
            }
        } catch (error) {
            console.error('Error en la petición:', error);
            mostrarMensaje('Error al conectar con el servidor', 'error');
        }
    }

    // Función para eliminar un grupo completo
    window.eliminarGrupo = async function(idGrupo) {
        if (confirm('¿Está seguro de que desea eliminar este grupo? Esta acción también eliminará todos los miembros del grupo y no se puede deshacer.')) {
            try {
                mostrarMensaje('Eliminando grupo...', 'info');
                
                const datos = {
                    accion: 'eliminarGrupo',
                    idGrupo: idGrupo
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
                    mostrarMensaje('¡Grupo eliminado exitosamente!', 'exito');
                    
                    // Refrescar la vista de grupos después de 2 segundos
                    setTimeout(() => {
                        verGrupos();
                    }, 2000);
                } else {
                    mostrarMensaje(resultado.mensaje || 'Error al eliminar el grupo', 'error');
                }
            } catch (error) {
                console.error('Error en la petición:', error);
                mostrarMensaje('Error al conectar con el servidor', 'error');
            }
        }
    }

    // Función para eliminar un miembro de un grupo
    window.eliminarMiembro = async function(idMiembroGrupo, nombreMiembro) {
        if (confirm(`¿Está seguro de que desea eliminar a "${nombreMiembro}" del grupo?`)) {
            try {
                mostrarMensaje('Eliminando miembro...', 'info');
                
                const datos = {
                    accion: 'eliminarMiembro',
                    idMiembroGrupo: idMiembroGrupo
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
                    mostrarMensaje(`¡${nombreMiembro} eliminado del grupo exitosamente!`, 'exito');
                    
                    // Refrescar la vista de grupos después de 2 segundos
                    setTimeout(() => {
                        verGrupos();
                    }, 2000);
                } else {
                    mostrarMensaje(resultado.mensaje || 'Error al eliminar el miembro', 'error');
                }
            } catch (error) {
                console.error('Error en la petición:', error);
                mostrarMensaje('Error al conectar con el servidor', 'error');
            }
        }
    }

    async function generarReporteGrupos() {
        try {
            alert('🔄 Generando reporte de grupos...');
            
            const datosReporte = {
                accion: 'generarReporteGrupos',
                idDocente: id,
                csrf_token: window.csrfToken || ''
            };

            const respuesta = await fetch('../../controllers/ReporteController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datosReporte)
            });

            if (!respuesta.ok) {
                throw new Error('Error en la respuesta del servidor');
            }

            const resultado = await respuesta.json();

            if (resultado.exito) {
                alert('✅ Reporte de grupos generado exitosamente');
                
                // Si se devuelve una URL del archivo, permitir descarga
                if (resultado.url_descarga) {
                    const link = document.createElement('a');
                    link.href = resultado.url_descarga;
                    link.download = resultado.nombre_archivo || 'reporte_grupos.pdf';
                    link.click();
                }
            } else {
                alert('❌ Error: ' + (resultado.mensaje || 'Error desconocido al generar reporte'));
            }

        } catch (err) {
            console.error('Error generando reporte de grupos:', err);
            alert('❌ Error de conexión al generar el reporte. Revisa la consola para más detalles.');
        }
    }

    // Evitar mostrar contenido vía "back" (segunda capa)
    window.history.pushState(null, '', window.location.href);
    window.onpopstate = function () { window.history.pushState(null, '', window.location.href); };
});