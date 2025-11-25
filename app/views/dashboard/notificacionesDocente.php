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
    
    <link rel="stylesheet" href="../../../public/css/dashboardAdministrador.css">
    <link rel="stylesheet" href="../../../public/css/swiper.css">
</head>
<body>
    <header>
        <a href="docente.php" class="logo">BibliONLINE</a>
        <ul class="navlist">
            <li><a href="panelGestionGrupos.php" class="lnk">Herramientas</a></li>
            <li><a href="panelGestionContenidoDocente.php">Subir Contenido</a></li>
            <li><a href="notificacionesDocente.php">Notificaciones</a></li>
            <li><a href="perfilDocente.php">Mi cuenta</a></li>
            <li><a href="" class="lnk" id="btnCerrarSesion">Cerrar Sesion</a></li>
        </ul>
        <div class="bx bx-menu" id="menu-icon"></div>
    </header>

    <br><br>

    <!-- Contenido principal -->
    <main class="contenido-principal">
        <div class="contenedor-notificaciones">
            <div id="contenedorNotificaciones">
                <!-- Las notificaciones se cargarán aquí dinámicamente -->
                <div class="cargando">
                    <p>Cargando notificaciones...</p>
                </div>
            </div>
        </div>
    </main>



    <!-- Modal para motivo de rechazo -->
    <div id="overlayModal" class="overlay-modal"></div>
    <div id="modalRechazo" class="modal-rechazo">
        <div class="modal-header">
            <h3>Rechazar Recurso</h3>
            <button onclick="cerrarModalRechazo()" class="btn-cerrar">&times;</button>
        </div>
        <div class="modal-body">
            <label for="motivoRechazo">Motivo del rechazo:</label>
            <textarea id="motivoRechazo" rows="4" placeholder="Explica por qué estás rechazando este recurso..."></textarea>
        </div>
        <div class="modal-footer">
            <button onclick="cerrarModalRechazo()" class="btn-cancelar">Cancelar</button>
            <button onclick="confirmarRechazo()" class="btn-confirmar">Confirmar Rechazo</button>
        </div>
    </div>

    <!-- Estilos CSS para las notificaciones -->
    <style>
        .contenido-principal {
            padding: 80px 20px 20px;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .contenedor-notificaciones {
            max-width: 1200px;
            margin: 0 auto;
        }

        .tarjeta-notificacion {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            transition: transform 0.3s ease;
        }

        .tarjeta-notificacion:hover {
            transform: translateY(-2px);
        }

        .info-recurso {
            flex: 1;
            margin-right: 20px;
        }

        .header-recurso {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .header-recurso h3 {
            margin: 0;
            color: #333;
            font-size: 1.3em;
        }

        .categoria-badge {
            background: #667eea;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
        }

        .descripcion {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .info-alumno {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            color: #555;
            line-height: 1.6;
        }

        .enlace-recurso {
            color: #555;
        }

        .enlace-recurso a {
            color: #667eea;
            text-decoration: none;
            word-break: break-all;
        }

        .enlace-recurso a:hover {
            text-decoration: underline;
        }

        .botones-accion {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn-aprobar, .btn-rechazar {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-width: 140px;
        }

        .btn-aprobar {
            background: #28a745;
            color: white;
        }

        .btn-aprobar:hover {
            background: #218838;
            transform: translateY(-1px);
        }

        .btn-rechazar {
            background: #dc3545;
            color: white;
        }

        .btn-rechazar:hover {
            background: #c82333;
            transform: translateY(-1px);
        }

        .sin-notificaciones {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .sin-notificaciones h3 {
            color: #667eea;
            margin-bottom: 15px;
        }

        .sin-notificaciones p {
            color: #666;
        }

        .cargando {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            color: #667eea;
        }

        /* Estilos del modal */
        .overlay-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .modal-rechazo {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            z-index: 1001;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            margin: 0;
            color: #333;
        }

        .btn-cerrar {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #999;
        }

        .btn-cerrar:hover {
            color: #333;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-body label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        .modal-body textarea {
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 12px;
            font-family: inherit;
            resize: vertical;
            min-height: 100px;
        }

        .modal-body textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.2);
        }

        .modal-footer {
            padding: 20px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn-cancelar, .btn-confirmar {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-cancelar {
            background: #6c757d;
            color: white;
        }

        .btn-cancelar:hover {
            background: #5a6268;
        }

        .btn-confirmar {
            background: #dc3545;
            color: white;
        }

        .btn-confirmar:hover {
            background: #c82333;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .tarjeta-notificacion {
                flex-direction: column;
                padding: 20px;
            }

            .info-recurso {
                margin-right: 0;
                margin-bottom: 20px;
            }

            .botones-accion {
                flex-direction: row;
                justify-content: space-between;
            }

            .btn-aprobar, .btn-rechazar {
                min-width: 120px;
            }

            .modal-rechazo {
                width: 95%;
                margin: 0 auto;
            }
        }
    </style>

    <script>window.csrfToken = '<?php echo $csrf; ?>';</script>
    <script src="../../../public/js/notificaciones.js"></script>

    <script>window.csrfToken = '<?php echo $csrf; ?>';</script>
    <script src="../../../public/js/dashboardDocente.js"></script>
    <script src="https://kit.fontawesome.com/b668f928a3.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
    <script src="../../../public/js/swiper.js"></script>
    <script src="../../../public/js/feed.js"></script>
</body>
</html>

