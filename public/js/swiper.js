let modal, modalCerrar, modalTitulo, modalDesc, modalUrlLink;
let btnFavorito, contenedorEstrellas, swiperWrapper, btnFavoritoTexto;

function cerrarModal() {
    if (modal) {
        modal.style.display = 'none';
    }
}

function resetearEstrellas() {
    if (contenedorEstrellas) {
        contenedorEstrellas.querySelectorAll('.fa-star').forEach(star => {
            star.classList.remove('fa-solid');
            star.classList.add('fa-regular');
        });
    }
}

function resetearEstadoModal() {
    if (btnFavorito) {
        btnFavorito.classList.remove('activo');
    }
    resetearEstrellas();
}

/**
 * Abre y llena el modal con los datos del recurso seleccionado.
 */
function abrirModal(datos) {
    if (!modal) return; 

    // Llenar contenido
    modalUrlLink.href = datos.archivoUrl || datos.fileUrl; 
    modalUrlLink.textContent = datos.archivoUrl || datos.fileUrl;
    modalTitulo.textContent = datos.titulo || datos.title;
    modalDesc.textContent = datos.descripcion || datos.description;
    
    // Guardamos el ID
    modal.dataset.currentId = datos.id || datos.id_recurso; 
    
    // 1. Reseteamos el estado visual
    resetearEstadoModal();

    // 2. Chequeo de estado de favorito (Aplica el color rojo y el texto correcto)
    const isFavorited = datos.esFavorito === 'true' || datos.favorito === 'true'; 
    if (isFavorited) {
        btnFavorito.classList.add('activo');
        btnFavoritoTexto.textContent = 'Quitar de Favoritos';
    } else {
        btnFavorito.classList.remove('activo');
        btnFavoritoTexto.textContent = 'Agregar a Favoritos';
    }

    modal.style.display = 'flex'; 
}


function pintarTarjetas(noticias) {
    if (!swiperWrapper) return;
    swiperWrapper.innerHTML = ''; 

    noticias.forEach(noticia => {
        const esFav = (noticia.es_favorito == 1) ? 'true' : 'false';
        const imgUrl = noticia.imagen_url || `https://picsum.photos/seed/${noticia.id_recurso}/300/180`;
        const idRecurso = noticia.id_recurso; 

        const tarjetaHtml = `
            <div class="swiper-slide" 
                 data-id="${idRecurso}" 
                 data-title="${noticia.titulo}" 
                 data-description="${noticia.descripcion}" 
                 data-archivo-url="${noticia.archivo_url}"
                 data-image-url="${imgUrl}"
                 data-es-favorito="${esFav}"> 
                
                <div class="tarjeta-noticia">
                    <img src="${imgUrl}" alt="${noticia.titulo}">
                    <div class="tarjeta-contenido">
                        <h3>${noticia.titulo}</h3>
                        <p>${noticia.descripcion.substring(0, 70)}...</p>
                    </div>
                </div>
            </div>
        `;
        swiperWrapper.innerHTML += tarjetaHtml;
    });
    
    inicializarCarrusel();
}

function inicializarCarrusel() {
    const swiper = new Swiper('.swiper', {
        slidesPerView: 1, 
        spaceBetween: 20, 
        loop: true,
        mousewheel: false,
        pagination: { el: '.swiper-pagination', clickable: true },
        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        breakpoints: {
            640: { slidesPerView: 3, spaceBetween: 20 },
            1024: { slidesPerView: 5, spaceBetween: 30 },
            1400: { slidesPerView: 5, spaceBetween: 30 }
        }
    });
}

async function cargarNoticias() {
    try {
        const token = window.csrfToken || ''; 
        const respuesta = await fetch('../../controllers/AuthController.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': token },
            body: JSON.stringify({ accion: 'obtenerNoticiasDestacadas' })
        });
        const resultado = await respuesta.json();
        if (resultado.exito && resultado.noticias) {
            pintarTarjetas(resultado.noticias);
        } else {
            console.error('No se pudieron cargar las noticias:', resultado.mensaje);
        }
    } catch (error) {
        console.error('Error en fetch:', error);
    }
}

async function enviarCalificacion(id, calificacion) {
    try {
        const respuesta = await fetch('../../controllers/AuthController.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': window.csrfToken },
            body: JSON.stringify({
                accion: 'calificarRecurso', 
                id_recurso: id,
                calificacion: calificacion
            })
        });

        if (!respuesta.ok) throw new Error('Error en la petición de calificación');
        const resultado = await respuesta.json();

        if (resultado.exito) {
            console.log('Calificación guardada');
        } else {
            console.warn('No se guardó la calificación:', resultado.mensaje);
            resetearEstrellas();
            alert('No se pudo guardar la calificación. Intenta de nuevo.'); 
        }
    } catch (err) {
        console.error('Error al guardar calificación:', err);
        resetearEstrellas(); 
    }
}

async function enviarFavorito(id, esFavorito) {
    try {
        const respuesta = await fetch('../../controllers/AuthController.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': window.csrfToken },
            body: JSON.stringify({
                accion: 'marcarFavorito', 
                id_recurso: id,
                es_favorito: esFavorito 
            })
        });

        if (!respuesta.ok) throw new Error('Error en la petición');
        const resultado = await respuesta.json();

        if (resultado.exito) {
            console.log('Favorito guardado');
        } else {
            console.warn('No se guardó favorito en servidor:', resultado.mensaje);
            
            // Deshacer el cambio visual y de data-attribute
            btnFavorito.classList.toggle('activo'); 
            btnFavoritoTexto.textContent = (!esFavorito) ? 'Quitar de Favoritos' : 'Agregar a Favoritos';
            const slide = document.querySelector(`.swiper-slide[data-id="${id}"]`);
            if (slide) slide.dataset.esFavorito = (!esFavorito) ? 'true' : 'false';
        }
    } catch (err) {
        console.error('Error al guardar favorito:', err);
        // Deshacer el cambio visual y de data-attribute en error de red
        btnFavorito.classList.toggle('activo');
        btnFavoritoTexto.textContent = (!esFavorito) ? 'Quitar de Favoritos' : 'Agregar a Favoritos';
        const slide = document.querySelector(`.swiper-slide[data-id="${id}"]`);
        if (slide) slide.dataset.esFavorito = (!esFavorito) ? 'true' : 'false';
    }
}

document.addEventListener('DOMContentLoaded', () => {

    // --- ASIGNACIÓN DE REFERENCIAS LOCALES ---
    modal = document.getElementById('modal-detalle');
    modalCerrar = document.querySelector('.modal-cerrar');
    modalTitulo = document.getElementById('modal-titulo'); 
    modalDesc = document.getElementById('modal-desc'); 
    modalUrlLink = document.getElementById('modal-url-link'); 
    btnFavorito = document.getElementById('btn-favorito');
    contenedorEstrellas = document.querySelector('.valoracion');
    swiperWrapper = document.getElementById('carrusel-wrapper');
    btnFavoritoTexto = document.getElementById('btn-favorito-texto');

cerrarModal();
    // --- EVENT LISTENERS ---

    swiperWrapper.addEventListener('click', (e) => {
        const slideClickeado = e.target.closest('.swiper-slide');
        if (slideClickeado) {
            abrirModal(slideClickeado.dataset); 
        }
    });

    modalCerrar.addEventListener('click', cerrarModal);
    
    modal.addEventListener('click', (e) => {
        if (e.target === modal) { 
            cerrarModal();
        }
    });

    contenedorEstrellas.addEventListener('click', (e) => {
        if (e.target.classList.contains('fa-star')) {
            const valor = parseInt(e.target.dataset.valor);
            const idNoticia = modal.dataset.currentId;
            resetearEstrellas();
            contenedorEstrellas.querySelectorAll('.fa-star').forEach(star => {
                if (parseInt(star.dataset.valor) <= valor) {
                    star.classList.add('fa-solid');
                    star.classList.remove('fa-regular');
                }
            });
            enviarCalificacion(idNoticia, valor);
        }
    });

    btnFavorito.addEventListener('click', () => {
        const idNoticia = modal.dataset.currentId;
        
        btnFavorito.classList.toggle('activo');
        const esFavorito = btnFavorito.classList.contains('activo');

        // Update visual text
        btnFavoritoTexto.textContent = esFavorito ? 'Quitar de Favoritos' : 'Agregar a Favoritos';

        // Update dataset of the slide (for persistence when reopening)
        const slide = document.querySelector(`.swiper-slide[data-id="${idNoticia}"]`);
        if (slide) {
            slide.dataset.esFavorito = esFavorito ? 'true' : 'false';
        }

        enviarFavorito(idNoticia, esFavorito);
    });


    // --- INICIO DE CARGA ---
    cargarNoticias();
});