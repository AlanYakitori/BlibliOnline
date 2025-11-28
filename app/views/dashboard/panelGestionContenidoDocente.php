<?php
require_once __DIR__ . '/../../../config/session.php';

protegerPagina(['docente']);
$csrf = obtenerCSRFToken();

// Tipo de usuario fijo para docente
$tipoUsuario = 'docente';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Recursos Educativos - Docente | BibliONLINE</title>

    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.7.0/fonts/remixicon.css" rel="stylesheet">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../../../public/css/gestionDocente.css">
    <link rel="stylesheet" href="../../../public/css/dashboardAdministrador.css">
    <link rel="stylesheet" href="../../../public/css/dashboardDocente.css">
    <link rel="stylesheet" href="../../../public/css/footer.css">
</head>
<body>
    <header>
        <a href="docente.php" class="logo">BibliONLINE</a>
        <ul class="navlist">
            <li><a href="panelGestionGrupos.php" class="lnk">Herramientas</a></li>
            <li><a href="panelGestionContenidoDocente.php" class="lnk">Mi Contenido</a></li>
            <li><a href="notificacionesDocente.php" class="lnk">Notificaciones</a></li>
            <li><a href="perfilDocente.php" class="lnk">Mi cuenta</a></li>
            <li><a href="#" class="lnk" id="btnCerrarSesion">Cerrar Sesión</a></li>
        </ul>

        <div class="bx bx-menu" id="menu-icon"></div>
    </header>

    <div class="wrapper">
        <div class="main-container">
            <div class="content1">
                <div class="formularioContenido">
                    <h3 class="tituloFormulario">Compartir Material Educativo</h3>
                    <p style="color: #666; margin-bottom: 20px;">Comparte recursos de alta calidad que serán aprobados automáticamente para beneficio de toda la comunidad educativa</p>
                    
                    <!-- Formulario para agregar nuevo contenido -->
                    <form class="camposFormulario" id="formularioContenido">
                        <div class="grupoInput">
                            <label class="labelInput" for="inputTitulo">Título del Material *</label>
                            <input type="text" id="inputTitulo" class="inputEstilizado" placeholder="Nombre descriptivo de tu material educativo" required>
                        </div>
                        
                        <div class="grupoInput">
                            <label class="labelInput" for="inputDescripcion">Descripción Pedagógica *</label>
                            <textarea id="inputDescripcion" class="textareaEstilizado" placeholder="Describe los objetivos de aprendizaje, contenido temático y aplicación práctica del material" rows="4" required></textarea>
                        </div>
                        
                        <div class="grupoInput">
                            <label class="labelInput" for="inputUrl">Enlace del Recurso *</label>
                            <input type="url" id="inputUrl" class="inputEstilizado" placeholder="https://drive.google.com/file/d/ejemplo o URL del recurso" required>
                        </div>
                        
                        <div class="grupoInput campoOpcional">
                            <label class="labelInput" for="inputImagenUrl">Imagen Representativa</label>
                            <input type="url" id="inputImagenUrl" class="inputEstilizado" placeholder="https://ejemplo.com/imagen-material.jpg">
                        </div>
                        
                        <div class="grupoInput">
                            <label class="labelInput" for="selectCategoria">Área de Conocimiento *</label>
                            <select id="selectCategoria" class="selectEstilizado" required>
                                <option value="">Seleccione la materia/área</option>
                                <option value="1">Tecnología e Información</option>
                                <option value="2">Ciencias Exactas y Naturales</option>
                                <option value="3">Ciencias Sociales y Humanidades</option>
                                <option value="4">Ingeniería y Aplicadas</option>
                                <option value="5">Educación y Pedagogía</option>
                                <option value="6">Idiomas y Cultura</option>
                                <option value="7">Administración y Negocios</option>
                                <option value="8">Energía y Medio Ambiente</option>
                                <option value="9">Biotecnología y Salud</option>
                                <option value="10">Manufactura y Producción</option>
                            </select>
                        </div>
                        
                        <input type="submit" value="Compartir Material" class="btnCrearRecurso" id="btnSubirContenido">
                    </form>
                </div>

                <!-- Contenedor para formulario de actualización -->
                <div id="contenedorActualizarContenido" class="contenedor-grupos" style="margin-top: 20px;"></div>
                
            </div>
            
            <div class="content2">
                <div class="subcontent1">
                    <p>Mi biblioteca de recursos</p>
                    <br>
                    <a id="btnVerContenido" class="btnGenerar">Ver</a>
                    
                    <!-- Contenedor para mostrar el contenido -->
                    <div id="contenedorContenido" class="contenedor-grupos" style="margin-top: 20px;"></div>
                </div>
            </div>
        </div>
    </div>

    <script>window.csrfToken = '<?php echo $csrf; ?>';</script>
    <script>window.tipoUsuario = '<?php echo $tipoUsuario; ?>';</script>
    <script src="../../../public/js/agregarContenido.js"></script>
    <script src="../../../public/js/dashboardAdministrador.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>

<?php include '../footer.php'; ?>