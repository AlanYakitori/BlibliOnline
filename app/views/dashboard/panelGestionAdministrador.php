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
    <title>Panel de Herramientas</title>

    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.7.0/fonts/remixicon.css" rel="stylesheet">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../../../public/css/dashboardAdministrador.css">
    <link rel="stylesheet" href="../../../public/css/footer.css">
    <link rel="stylesheet" href="../../../public/css/gestionAdministrador.css">
</head>
<body>
    <header>
        <a href="administrador.php" class="logo">BibliONLINE</a>
        <ul class="navlist">
            <li><a href="panelGestionAdministrador.php" class="lnk active">Herramientas</a></li>
            <li><a href="#" class="lnk">Subir contenido</a></li>
            <li><a href="#" class="lnk">Notificaciones</a></li>
            <li><a href="perfil.php" class="lnk">Mi cuenta</a></li>
            <li><a href="#" class="lnk" id="btnCerrarSesion">Cerrar Sesion</a></li>
        </ul>
        <div class="bx bx-menu" id="menu-icon"></div>
    </header>

    <div class="main-wrapper">
        
        <div class="dashboard-header">
            <h1>Panel de Administración</h1>
            <p>Gestiona la base de datos, usuarios y reportes del sistema.</p>
        </div>

        <div class="cards-grid">
            
            <div class="admin-card">
                <div class="card-icon"><i class="ri-database-2-line"></i></div>
                <h3>Copia de Seguridad</h3>
                <p>Genera un archivo .bak con toda la información actual de la base de datos.</p>
                <a id="btnBackup" class="btn-action btn-blue">
                    <i class="bx bx-download"></i> Generar Backup
                </a>
            </div>

            <div class="admin-card">
                <div class="card-icon"><i class="ri-history-line"></i></div>
                <h3>Restaurar Sistema</h3>
                <p>Sube un archivo .bak para restaurar la base de datos a un estado anterior.</p>
                
                <form class="formularioDB" id="formularioDB">
                    <div class="file-input-wrapper">
                        <input type="file" id="inputArchivoDB" name="inputArchivoDB" accept=".bak" required>
                    </div>
                    <button type="submit" class="btn-action btn-warning" id="btnRestaurarDB">
                        <i class="bx bx-upload"></i> Restaurar
                    </button>
                </form>
            </div>

            <div class="admin-card">
                <div class="card-icon"><i class="ri-file-chart-line"></i></div>
                <h3>Reportes Generales</h3>
                <p>Descarga un PDF con las estadísticas de uso y registros del sistema.</p>
                <a href="#" class="btn-action btn-green">
                    <i class="bx bxs-file-pdf"></i> Generar Reporte
                </a>
            </div>

            <div class="admin-card">
                <div class="card-icon"><i class="ri-user-settings-line"></i></div>
                <h3>Gestión de Usuarios</h3>
                <p>Consulta, edita o elimina usuarios (Docentes y Alumnos) registrados.</p>
                <a href="consultarAdministrador.php" class="btn-action btn-dark">
                    <i class="bx bx-user"></i> Gestionar Usuarios
                </a>
            </div>

        </div>
    </div>

    <br><br><br>

    <?php include '../footer.php'; ?>

    <script>window.csrfToken = '<?php echo $csrf; ?>';</script>
    <script src="../../../public/js/dashboardAdministrador.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>