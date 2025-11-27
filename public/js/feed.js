document.addEventListener('DOMContentLoaded', () => {

    const feedContainer = document.getElementById('recursos-feed-container');
    const spinner = document.getElementById('loading-spinner');
    
    // Variables de estado del scroll infinito
    let currentPage = 0;
    const limit = 12; 
    let isLoading = false;
    let hasMore = true; 

    // --- LÓGICA DE CARGA (FETCH & PAGINACIÓN) ---
    async function cargarFeed() {
        if (isLoading || !hasMore) return;
        
        isLoading = true;
        spinner.style.display = 'block';
        
        const offset = currentPage * limit;
        
        try {
            const token = window.csrfToken || ''; 
            const respuesta = await fetch('../../controllers/ContenidoController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': token },
                body: JSON.stringify({ 
                    accion: 'obtenerFeedRecursos',
                    limit: limit,
                    offset: offset
                })
            });

            const resultado = await respuesta.json();
            
            if (resultado.exito && resultado.recursos) {
                
                if (resultado.recursos.length === 0) {
                    hasMore = false; 
                } else {
                    pintarRecursos(resultado.recursos);
                    currentPage++; 
                }
            } else {
                 hasMore = false;
                 console.error(resultado.mensaje || 'Error al cargar feed.');
            }

        } catch (error) {
            console.error('Error de red al cargar el feed:', error);
            hasMore = false;
        } finally {
            isLoading = false;
            if (!hasMore) {
                spinner.style.display = 'none';
            }
        }
    }
    
    // Detector de Scroll
    function handleScroll() {
        const scrollThreshold = 500;
        if (window.scrollY + window.innerHeight >= document.documentElement.scrollHeight - scrollThreshold && !isLoading && hasMore) {
            cargarFeed();
        }
    }
    
    // Pintar el contenido
    function pintarRecursos(recursos) {
        let html = '';
        recursos.forEach(recurso => {
            const esFav = (recurso.es_favorito == 1) ? 'true' : 'false';
            const calificacionUsuario = recurso.calificacion_usuario || 0;
            const imgUrl = recurso.imagen_url || `https://picsum.photos/seed/${recurso.id_recurso}/300/180`;

            // Incluimos todos los datos para que el modal funcione
            html += `
                <div class="tarjeta-noticia" 
                     data-id="${recurso.id_recurso}" 
                     data-title="${recurso.titulo}" 
                     data-description="${recurso.descripcion}" 
                     data-image-url="${imgUrl}"
                     data-file-url="${recurso.archivo_url}" 
                     data-favorito="${esFav}"
                     data-calificacion-usuario="${calificacionUsuario}">
                    
                    <img src="${imgUrl}" alt="${recurso.titulo}">
                    <div class="tarjeta-contenido">
                        <h3>${recurso.titulo}</h3>
                        <p>${recurso.descripcion.substring(0, 70)}...</p>
                    </div>
                </div>
            `;
        });
        feedContainer.insertAdjacentHTML('beforeend', html);
    }
    
    // --- DELEGACIÓN DE EVENTOS (Abre el Modal) ---
    feedContainer.addEventListener('click', (e) => {
        const card = e.target.closest('.tarjeta-noticia');
        if (!card) return;

        // ¡LLAMADA EXITOSA A LA FUNCIÓN GLOBAL!
        if (typeof abrirModal === 'function') {
            abrirModal(card.dataset);
        } else {
            console.error("Error: Función 'abrirModal' no disponible.");
        }
    });


    // --- INICIO ---
    cargarFeed();
    window.addEventListener('scroll', handleScroll);
});