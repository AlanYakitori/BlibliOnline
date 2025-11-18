// ===== GESTIÓN DE CONTENIDO =====

// Variables globales
let datosUsuario = null;
let contenidoEnEdicion = null;

// Inicialización cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    const usuarioActualStorage = localStorage.getItem('usuarioActual');
    const nombreBienvenida = document.getElementById('nombreBienvenida');

    if (usuarioActualStorage) {
        try { 
            const datosUsuarioTemp = JSON.parse(usuarioActualStorage);

            const nombre = datosUsuarioTemp.nombre.trim();
            if (nombreBienvenida) {
                nombreBienvenida.textContent = `Bienvenido ${nombre}`;
            }

            // Establecer variable global con los datos del usuario
            datosUsuario = {
                id: datosUsuarioTemp.id,
                tipo_usuario: datosUsuarioTemp.tipoUsuario,
                nombre: datosUsuarioTemp.nombre
            };

        } catch(e){
            console.warn('usuarioActual corrupto en localStorage');
            alert('Error: No se pudieron obtener los datos del usuario');
            return;
        }
    } else {
        alert('Error: No se pudieron obtener los datos del usuario');
        return;
    }

    // Configurar eventos
    configurarEventos();
});

function configurarEventos() {
    // Evento para enviar formulario de contenido
    const formularioContenido = document.getElementById('formularioContenido');
    if (formularioContenido) {
        formularioContenido.addEventListener('submit', function(e) {
            e.preventDefault();
            agregarContenido();
        });
    }

    // Evento para ver contenido
    const btnVerContenido = document.getElementById('btnVerContenido');
    if (btnVerContenido) {
        btnVerContenido.addEventListener('click', function() {
            consultarContenido();
        });
    }

    // Evento para cerrar sesión
    const btnCerrarSesion = document.getElementById('btnCerrarSesion');
    if (btnCerrarSesion) {
        btnCerrarSesion.addEventListener('click', function(e) {
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
        alert('Error de conexión al cerrar sesión');
    }
}

// Función para agregar nuevo contenido
async function agregarContenido() {
    if (!datosUsuario) {
        alert('Error: No se pudieron obtener los datos del usuario');
        return;
    }

    try {
        // Obtener datos del formulario
        const titulo = document.getElementById('inputTitulo').value.trim();
        const descripcion = document.getElementById('inputDescripcion').value.trim();
        const archivo_url = document.getElementById('inputUrl').value.trim();
        const imagen_url = document.getElementById('inputImagenUrl').value.trim();
        const id_categoria = document.getElementById('selectCategoria').value;

        // Validar campos obligatorios
        if (!titulo || !descripcion || !archivo_url || !id_categoria) {
            alert('Los campos título, descripción, URL del archivo y categoría son obligatorios');
            return;
        }

        // Preparar datos para enviar
        const datos = {
            accion: 'agregarContenido',
            titulo: titulo,
            descripcion: descripcion,
            archivo_url: archivo_url,
            imagen_url: imagen_url, // Campo opcional
            id_categoria: parseInt(id_categoria),
            id_usuario: datosUsuario.id,
            tipo_usuario: datosUsuario.tipo_usuario
        };

        // Enviar datos al servidor
        const respuesta = await fetch('../../controllers/ContenidoController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datos)
        });

        const resultado = await respuesta.json();

        if (resultado.exito) {
            alert(resultado.mensaje);
            // Limpiar formulario
            document.getElementById('formularioContenido').reset();
            // Actualizar lista de contenido
            consultarContenido();
        } else {
            alert(resultado.mensaje);
        }

    } catch (error) {
        console.error('Error al agregar contenido:', error);
        alert('Error de conexión al servidor');
    }
}

// Función para consultar contenido del usuario
async function consultarContenido() {
    if (!datosUsuario) {
        alert('Error: No se pudieron obtener los datos del usuario');
        return;
    }

    try {
        const datos = {
            accion: 'consultarContenido',
            id_usuario: datosUsuario.id
        };

        const respuesta = await fetch('../../controllers/ContenidoController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datos)
        });

        const resultado = await respuesta.json();

        if (resultado.exito) {
            mostrarContenido(resultado.contenidos);
        } else {
            alert(resultado.mensaje);
        }

    } catch (error) {
        console.error('Error al consultar contenido:', error);
        alert('Error de conexión al servidor');
    }
}

// Función para mostrar el contenido en la interfaz (diseño tipo tarjeta moderno)
function mostrarContenido(contenidos) {
    const contenedor = document.getElementById('contenedorContenido');
    if (!contenedor) return;

    if (!contenidos || contenidos.length === 0) {
        contenedor.innerHTML = '<p style="color: #999; font-style: italic; text-align: center;">No has subido contenido aún.</p>';
        return;
    }

    let html = '';
    contenidos.forEach(contenido => {
        // Mostrar calificación para todos los usuarios, pero solo cuando esté disponible
        const calificacionHtml = contenido.calificacion && contenido.calificacion > 0 ? 
            `<div class="contenido-calificacion"><strong>Calificación:</strong> ${contenido.calificacion} ⭐</div>` : '';
        
        // Mostrar estado solo para alumnos
        const estadoHtml = datosUsuario.tipo_usuario === 'alumno' ? 
            `<div class="contenido-estado"><strong>Estado:</strong> <span class="estado-${obtenerClaseEstado(contenido.aprobado)}">${obtenerTextoEstado(contenido.aprobado)}</span></div>` : '';

        html += `
            <div class="contenido-tarjeta">
                <div class="contenido-header">
                    <h3 class="contenido-titulo">${contenido.titulo}</h3>
                    <div class="contenido-acciones">
                        <button class="btn-actualizar" onclick="mostrarFormularioActualizacion(${contenido.id_recurso}, '${contenido.titulo.replace(/'/g, "\\'")}', '${contenido.descripcion.replace(/'/g, "\\'")}', '${contenido.archivo_url}', '${contenido.imagen_url || ''}', ${getIdCategoriaByName('${contenido.categoria}')})">Actualizar</button>
                        <button class="btn-eliminar" onclick="eliminarContenido(${contenido.id_recurso})">Eliminar</button>
                    </div>
                </div>
                
                <div class="contenido-info">
                    <div class="contenido-descripcion"><strong>Descripción:</strong> ${contenido.descripcion}</div>
                    
                    <div class="contenido-url"><strong>URL:</strong> 
                        <a href="${contenido.archivo_url}" target="_blank" class="enlace-contenido">${contenido.archivo_url}</a>
                    </div>
                    
                    <div class="contenido-categoria"><strong>Categoría:</strong> ${contenido.categoria}</div>
                    
                    ${calificacionHtml}
                    ${estadoHtml}
                </div>
            </div>
        `;
    });

    contenedor.innerHTML = html;
}

// Función auxiliar para obtener ID de categoría por nombre
function getIdCategoriaByName(nombreCategoria) {
    const categorias = {
        'Tecnología e Información': 1,
        'Ciencias Exactas y Naturales': 2,
        'Ciencias Sociales y Humanidades': 3,
        'Ingeniería y Aplicadas': 4,
        'Educación y Pedagogía': 5,
        'Idiomas y Cultura': 6,
        'Administración y Negocios': 7,
        'Energía y Medio Ambiente': 8,
        'Biotecnología y Salud': 9,
        'Manufactura y Producción': 10
    };
    return categorias[nombreCategoria] || 1;
}

// Función para obtener la clase CSS del estado
function obtenerClaseEstado(aprobado) {
    if (aprobado === null) return 'espera';
    if (aprobado === 1) return 'aceptado';
    return 'rechazado';
}

// Función para obtener el texto del estado
function obtenerTextoEstado(aprobado) {
    if (aprobado === null) return 'En espera';
    if (aprobado === 1) return 'Aceptado';
    return 'Rechazado';
}

// Función para mostrar formulario de actualización (estilo similar a grupos)
function mostrarFormularioActualizacion(idRecurso, titulo, descripcion, archivoUrl, imagenUrl, idCategoria) {
    const contenedor = document.getElementById('contenedorActualizarContenido');
    if (!contenedor) return;

    contenidoEnEdicion = idRecurso;

    contenedor.innerHTML = `
        <div class="formulario-actualizar-grupo">
            <h3>Actualizar Contenido</h3>
            <form id="formularioActualizacion">
                <label for="inputTituloActualizar">Título del contenido:</label>
                <input type="text" id="inputTituloActualizar" value="${titulo}" required>
                
                <label for="inputDescripcionActualizar">Descripción:</label>
                <textarea id="inputDescripcionActualizar" rows="3" required>${descripcion}</textarea>
                
                <label for="inputUrlActualizar">URL del archivo:</label>
                <input type="url" id="inputUrlActualizar" value="${archivoUrl}" required>
                
                <label for="inputImagenUrlActualizar">URL de la imagen (opcional):</label>
                <input type="url" id="inputImagenUrlActualizar" value="${imagenUrl || ''}" placeholder="https://ejemplo.com/imagen.jpg (Opcional)">
                
                <label for="selectCategoriaActualizar">Categoría:</label>
                <select id="selectCategoriaActualizar" required>
                    <option value="1" ${idCategoria == 1 ? 'selected' : ''}>Tecnología e Información</option>
                    <option value="2" ${idCategoria == 2 ? 'selected' : ''}>Ciencias Exactas y Naturales</option>
                    <option value="3" ${idCategoria == 3 ? 'selected' : ''}>Ciencias Sociales y Humanidades</option>
                    <option value="4" ${idCategoria == 4 ? 'selected' : ''}>Ingeniería y Aplicadas</option>
                    <option value="5" ${idCategoria == 5 ? 'selected' : ''}>Educación y Pedagogía</option>
                    <option value="6" ${idCategoria == 6 ? 'selected' : ''}>Idiomas y Cultura</option>
                    <option value="7" ${idCategoria == 7 ? 'selected' : ''}>Administración y Negocios</option>
                    <option value="8" ${idCategoria == 8 ? 'selected' : ''}>Energía y Medio Ambiente</option>
                    <option value="9" ${idCategoria == 9 ? 'selected' : ''}>Biotecnología y Salud</option>
                    <option value="10" ${idCategoria == 10 ? 'selected' : ''}>Manufactura y Producción</option>
                </select>

                <div class="botones-formulario">
                    <input type="submit" value="Actualizar Contenido" class="btnGenerar">
                    <button type="button" class="btnCancelar" onclick="cancelarActualizacion()">Cancelar</button>
                </div>
            </form>
        </div>
    `;

    contenedor.style.display = 'block';

    // Configurar evento del formulario de actualización
    const formularioActualizacion = document.getElementById('formularioActualizacion');
    formularioActualizacion.addEventListener('submit', function(e) {
        e.preventDefault();
        actualizarContenido();
    });
}

// Función para actualizar contenido
async function actualizarContenido() {
    if (!datosUsuario) {
        alert('Error: No se pudieron obtener los datos del usuario');
        return;
    }

    try {
        // Obtener datos del formulario de actualización
        const titulo = document.getElementById('inputTituloActualizar').value.trim();
        const descripcion = document.getElementById('inputDescripcionActualizar').value.trim();
        const archivo_url = document.getElementById('inputUrlActualizar').value.trim();
        const imagen_url = document.getElementById('inputImagenUrlActualizar').value.trim();
        const id_categoria = document.getElementById('selectCategoriaActualizar').value;

        // Validar campos obligatorios
        if (!titulo || !descripcion || !archivo_url || !id_categoria) {
            alert('Los campos título, descripción, URL del archivo y categoría son obligatorios');
            return;
        }

        // Preparar datos para enviar
        const datos = {
            accion: 'actualizarContenido',
            id_recurso: contenidoEnEdicion,
            titulo: titulo,
            descripcion: descripcion,
            archivo_url: archivo_url,
            imagen_url: imagen_url, // Campo opcional
            id_categoria: parseInt(id_categoria),
            tipo_usuario: datosUsuario.tipo_usuario
        };

        // Enviar datos al servidor
        const respuesta = await fetch('../../controllers/ContenidoController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datos)
        });

        const resultado = await respuesta.json();

        if (resultado.exito) {
            alert(resultado.mensaje);
            cancelarActualizacion();
            consultarContenido();
        } else {
            alert(resultado.mensaje);
        }

    } catch (error) {
        console.error('Error al actualizar contenido:', error);
        alert('Error de conexión al servidor');
    }
}

// Función para cancelar actualización
function cancelarActualizacion() {
    const contenedor = document.getElementById('contenedorActualizarContenido');
    if (contenedor) {
        contenedor.style.display = 'none';
        contenedor.innerHTML = '';
    }
    contenidoEnEdicion = null;
}

// Función para eliminar contenido
async function eliminarContenido(idRecurso) {
    if (!confirm('¿Estás seguro de que deseas eliminar este contenido? Esta acción no se puede deshacer.')) {
        return;
    }

    try {
        const datos = {
            accion: 'eliminarContenido',
            id_recurso: idRecurso
        };

        const respuesta = await fetch('../../controllers/ContenidoController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datos)
        });

        const resultado = await respuesta.json();

        if (resultado.exito) {
            alert(resultado.mensaje);
            consultarContenido();
        } else {
            alert(resultado.mensaje);
        }

    } catch (error) {
        console.error('Error al eliminar contenido:', error);
        alert('Error de conexión al servidor');
    }
}
