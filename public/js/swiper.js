document.addEventListener('DOMContentLoaded', () => {

    

    const modal = document.getElementById('modal-detalle');
    const modalCerrar = document.querySelector('.modal-cerrar');
    const modalTitulo = document.getElementById('modal-titulo'); 
    const modalDesc = document.getElementById('modal-desc');     
    const modalUrlLink = document.getElementById('modal-url-link'); 
    const btnFavorito = document.getElementById('btn-favorito');
    const contenedorEstrellas = document.querySelector('.valoracion');
    const swiperWrapper = document.getElementById('carrusel-wrapper');
    
    async function cargarNoticias() {
        cerrarModal();
        try {
            const token = window.csrfToken || ''; 
            const respuesta = await fetch('../../controllers/AuthController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json', 'X-CSRF-Token': token
                },
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

    function pintarTarjetas(noticias) {
        if (!swiperWrapper) return;
        swiperWrapper.innerHTML = ''; 

        noticias.forEach(noticia => {
            const esFav = (noticia.es_favorito == 1) ? 'true' : 'false';

            const tarjetaHtml = `
                <div class="swiper-slide" 
                     data-id="${noticia.id_recurso}" 
                     data-titulo="${noticia.titulo}" 
                     data-img="${noticia.archivo_url}" 
                     data-desc="${noticia.descripcion}"
                     data-archivo-url="${noticia.archivo_url}"
                     
                     data-es-favorito="${esFav}"> 
                    
                    <div class="tarjeta-noticia">
                        <img src="https://picsum.photos/seed/${noticia.id_recurso}/300/180" alt="${noticia.titulo}">
                        
                        <div class="tarjeta-contenido">
                            <h3>${noticia.titulo}</h3>
                            <p>${noticia.descripcion}</p>
                        </div>
                    </div>
                </div>
            `;
            swiperWrapper.innerHTML += tarjetaHtml;
        });
        
        inicializarCarrusel();
    }

    function abrirModal(datos) {
        modalUrlLink.href = datos.archivoUrl;
        modalUrlLink.textContent = datos.archivoUrl;
        modalTitulo.textContent = datos.titulo;
        modalDesc.textContent = datos.desc;
        modal.dataset.currentId = datos.id; 
        resetearEstadoModal();
        if (datos.esFavorito === 'true') {
            btnFavorito.classList.add('activo'); 
        } else {
            btnFavorito.classList.remove('activo');
        }

        modal.style.display = 'flex';
    }


    function resetearEstadoModal() {
        btnFavorito.classList.remove('activo');
        resetearEstrellas(); 
    }
    
    function inicializarCarrusel() {
        const swiper = new Swiper('.swiper', {
            slidesPerView: 1, 
            spaceBetween: 20, 
            loop: true,
            mousewheel: true,
            pagination: { el: '.swiper-pagination', clickable: true },
            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
            breakpoints: {
                640: { slidesPerView: 3, spaceBetween: 20 },
                1024: { slidesPerView: 5, spaceBetween: 30 },
                1400: { slidesPerView: 5, spaceBetween: 30 }
            }
        });
    }

    function cerrarModal() {
        modal.style.display = 'none';
    }

    function resetearEstrellas() {
        contenedorEstrellas.querySelectorAll('.fa-star').forEach(star => {
            star.classList.remove('fa-solid');
            star.classList.add('fa-regular');
        });
    }

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

        const slide = document.querySelector(`.swiper-slide[data-id="${idNoticia}"]`);
        if (slide) {
            slide.dataset.esFavorito = esFavorito ? 'true' : 'false';
        }

        console.log(`Enviando favorito: ${esFavorito} para ID: ${idNoticia}`);
        enviarFavorito(idNoticia, esFavorito);
    });

    async function enviarCalificacion(id, calificacion) {
        
        const respuesta = await fetch('../../controllers/AuthController.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': window.csrfToken },
            body: JSON.stringify({
                accion: 'calificarRecurso', 
                id_recurso: id,
                calificacion: calificacion
            })
        });
        const resultado = await respuesta.json();
        if (resultado.exito) {
            console.log('Calificación guardada');
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
                btnFavorito.classList.toggle('activo'); // deshacer el toggle
                const slide = document.querySelector(`.swiper-slide[data-id="${id}"]`);
                if (slide) slide.dataset.esFavorito = (!esFavorito) ? 'true' : 'false';
            }
        } catch (err) {
            console.error('Error al guardar favorito:', err);
            btnFavorito.classList.toggle('activo');
            const slide = document.querySelector(`.swiper-slide[data-id="${id}"]`);
            if (slide) slide.dataset.esFavorito = (!esFavorito) ? 'true' : 'false';
        }
    }


    cargarNoticias();
});