<?php
require_once __DIR__ . '/../../../config/session.php';

protegerPagina(['administrador']);
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
    
    <link rel="stylesheet" href="../../../public/css/gestionAdministrador.css">
    <link rel="stylesheet" href="../../../public/css/gestionDocente.css">
    <link rel="stylesheet" href="../../../public/css/dashboardAdministrador.css">
    <link rel="stylesheet" href="../../../public/css/footer.css">
</head>
<body>
    <header>
        <a href="administrador.php" class="logo">BibliONLINE</a>
        <ul class="navlist">
            <li><a href="panelGestionAdministrador.php" class="lnk">Herramientas</a></li>
            <li><a href="panelGestionContenidoAdministrador.php" class="lnk">Subir Contenido</a></li>
            <li><a href="#" class="lnk">Notificaciones</a></li>
            <li><a href="perfilAdministrador.php" class="lnk">Mi cuenta</a></li>
            <li><a href="#" class="lnk" id="btnCerrarSesion">Cerrar Sesión</a></li>
        </ul>

        <div class="bx bx-menu" id="menu-icon"></div>

    </header>

    <div class="wrapper">
        <div class="main-container">
            <div class="content1">
                <!-- Sección de Copia de Seguridad -->
                <div class="formularioContenido">
                    <h3 class="tituloFormulario">Generar Copia de Seguridad</h3>
                    <p style="color: #666; margin-bottom: 20px;">Crea una copia de seguridad de todos los datos del sistema para garantizar la integridad de la información</p>
                    
                    <div class="camposFormulario">
                        <div class="grupoInput">
                            <label class="labelInput">Archivo de respaldo</label>
                            <p style="font-size: 14px; color: #777; margin: 5px 0 10px 0;">Se generará automáticamente un archivo .sql con toda la información del sistema</p>
                            <button id="btnBackup" class="btnCrearRecurso">Crear Copia de Seguridad</button>
                        </div>
                    </div>
                </div>

                <!-- Sección de Restauración -->
                <div class="formularioContenido">
                    <h3 class="tituloFormulario">Restaurar Datos del Sistema</h3>
                    <p style="color: #666; margin-bottom: 20px;">Seleccione un archivo .bak para realizar la restauración de datos del sistema</p>
                    
                    <form class="camposFormulario" id="formularioDB">
                        <div class="grupoInput">
                            <label class="labelInput" for="inputArchivoDB">Archivo de Respaldo *</label>
                            <input type="file" id="inputArchivoDB" name="inputArchivoDB" class="inputEstilizado" accept=".bak,.sql" required>
                            <p style="font-size: 12px; color: #999; margin-top: 5px;">Formatos aceptados: .bak, .sql</p>
                        </div>
                        <input type="submit" value="Restaurar Datos" class="btnCrearRecurso" id="btnRestaurarDB">
                    </form>
                </div>

                <!-- Sección de Reportes -->
                <div class="formularioContenido">
                    <h3 class="tituloFormulario">Generar Reporte General</h3>
                    <p style="color: #666; margin-bottom: 20px;">Genera un informe completo del estado actual del sistema y estadísticas de uso</p>
                    
                    <div class="camposFormulario">
                        <div class="grupoInput">
                            <label class="labelInput">Reporte del sistema</label>
                            <p style="font-size: 14px; color: #777; margin: 5px 0 10px 0;">Incluye estadísticas de usuarios, contenidos, grupos y actividad general</p>
                            <button id="btnReporte" class="btnCrearRecurso">Generar Reporte</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content2">
                <div class="subcontent1">
                    <div class="formularioContenido">
                        <h3 class="tituloFormulario">Gestionar Usuarios</h3>
                        <p style="color: #666; margin-bottom: 20px;">Administra todos los usuarios del sistema (editar, eliminar, ver perfiles)</p>
                        
                        <div class="camposFormulario">
                            <div class="grupoInput">
                                <label class="labelInput">Panel de administración</label>
                                <p style="font-size: 14px; color: #777; margin: 5px 0 10px 0;">Accede al panel completo de gestión de usuarios del sistema</p>
                                <a href="consultarAdministrador.php" class="btnCrearRecurso" style="display: inline-block; text-decoration: none; text-align: center;">Gestionar Usuarios</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>window.csrfToken = '<?php echo $csrf; ?>';</script>
    <script src="../../../public/js/dashboardAdministrador.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>

<?php include '../footer.php'; ?>
