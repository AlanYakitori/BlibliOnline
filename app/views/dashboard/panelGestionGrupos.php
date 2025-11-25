<?php
require_once __DIR__ . '/../../../config/session.php';

protegerPagina(['docente']);
$csrf = obtenerCSRFToken();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

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
            <li><a href="panelGestionContenidoDocente.php" class="lnk">Subir Contenido</a></li>
            <li><a href="notificacionesDocente.php" class="lnk">Notificaciones</a></li>
            <li><a href="perfilDocente.php" class="lnk">Mi cuenta</a></li>
            <li><a href="#" class="lnk" id="btnCerrarSesion">Cerrar Sesión</a></li>
        </ul>

        <div class="bx bx-menu" id="menu-icon"></div>

    </header>

    <div class="wrapper">
        <div class="main-container">
            <div class="content1">
                <!-- Sección de Crear Grupo -->
                <div class="formularioContenido">
                    <h3 class="tituloFormulario">Crear Nuevo Grupo</h3>
                    <p style="color: #666; margin-bottom: 20px;">Crea un grupo de trabajo para organizar a los estudiantes en proyectos colaborativos</p>
                    
                    <form class="camposFormulario" id="formularioGrupo">
                        <div class="grupoInput">
                            <label class="labelInput" for="inputNombreGrupo">Nombre del Grupo *</label>
                            <input type="text" id="inputNombreGrupo" class="inputEstilizado" placeholder="Ingresa el nombre del grupo de trabajo" required>
                            <p style="font-size: 12px; color: #999; margin-top: 5px;">El nombre debe ser descriptivo y único</p>
                        </div>
                        <input type="submit" value="Crear Nuevo Grupo" class="btnCrearRecurso" id="btnCrearGrupo">
                    </form>
                </div>

                <!-- Contenedor para formulario de actualización de grupos -->
                <div id="contenedorActualizarGrupos" class="contenedor-grupos" style="margin-top: 20px;"></div>

                <!-- Sección de Reportes -->
                <div class="formularioContenido">
                    <h3 class="tituloFormulario">Generar Reporte de Actividades</h3>
                    <p style="color: #666; margin-bottom: 20px;">Genera un informe detallado de las actividades y progreso de tus grupos</p>
                    
                    <div class="camposFormulario">
                        <div class="grupoInput">
                            <label class="labelInput">Reporte de grupos</label>
                            <p style="font-size: 14px; color: #777; margin: 5px 0 10px 0;">Incluye estadísticas de participación, contenidos compartidos y actividad general</p>
                            <button id="btnReporteGrupos" class="btnCrearRecurso">Generar Reporte</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content2">
                <div class="subcontent1">
                    <div class="formularioContenido">
                        <h3 class="tituloFormulario">Mis Grupos</h3>
                        <p style="color: #666; margin-bottom: 20px;">Visualiza y administra todos los grupos que has creado</p>
                        
                        <div class="camposFormulario">
                            <div class="grupoInput">
                                <label class="labelInput">Panel de grupos</label>
                                <p style="font-size: 14px; color: #777; margin: 5px 0 10px 0;">Ver todos tus grupos, miembros y gestionar configuraciones</p>
                                <button id="btnGrupos" class="btnCrearRecurso">Ver Mis Grupos</button>
                            </div>
                        </div>

                        <!-- Contenedor para mostrar los grupos -->
                        <div id="contenedorGrupos" class="contenedor-grupos" style="margin-top: 20px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>window.csrfToken = '<?php echo $csrf; ?>';</script>
    <script src="../../../public/js/dashboardDocente.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>

<?php include '../footer.php'; ?>
