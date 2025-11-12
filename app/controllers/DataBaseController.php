<?php
require_once __DIR__ . '/../models/DataBaseModel.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../config/session.php';

// Protección: solo administradores pueden ejecutar backups
protegerPagina(['administrador']);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['download'])) {
    $download = basename($_GET['download']); // sanitizar
    $backupsDir = realpath(__DIR__ . '/../../backups');
    if ($backupsDir === false) {
        http_response_code(404);
        echo 'Archivo no encontrado';
        exit;
    }
    $filePath = $backupsDir . DIRECTORY_SEPARATOR . $download;
    if (!file_exists($filePath)) {
        http_response_code(404);
        echo 'Archivo no encontrado';
        exit;
    }

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="copiaSeguridad.bak"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);

    @unlink($filePath);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Configurar headers JSON
    header('Content-Type: application/json');
    
    $contenType = $_SERVER['CONTENT_TYPE'] ?? '';
    
    // Si es multipart/form-data (subida de archivos), manejar restauración
    if (strpos($contenType, 'multipart/form-data') === 0) {
        if (!isset($_POST['accion']) || $_POST['accion'] !== 'restaurarBackup') {
            echo json_encode(['exito' => false, 'mensaje' => 'Acción no válida']);
            exit;
        }
        
        // Validar que se subió un archivo
        if (!isset($_FILES['backupFile']) || $_FILES['backupFile']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['exito' => false, 'mensaje' => 'Es necesario subir un archivo .bak para la restauración']);
            exit;
        }
        
        $archivo = $_FILES['backupFile'];
        
        // Validar extensión .bak
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        if ($extension !== 'bak') {
            echo json_encode(['exito' => false, 'mensaje' => 'Solo se permiten archivos .bak']);
            exit;
        }
        
        // Crear carpeta de uploads si no existe
        $uploadsDir = __DIR__ . '/../../uploads';
        if (!is_dir($uploadsDir)) {
            if (!mkdir($uploadsDir, 0755, true)) {
                echo json_encode(['exito' => false, 'mensaje' => 'No se pudo crear la carpeta de uploads']);
                exit;
            }
        }
        
        // Generar nombre único para el archivo temporal
        $timestamp = date('Ymd_His');
        $tempBakFile = $uploadsDir . DIRECTORY_SEPARATOR . "restore_{$timestamp}.bak";
        $tempSqlFile = $uploadsDir . DIRECTORY_SEPARATOR . "restore_{$timestamp}.sql";
        
        // Mover archivo subido
        if (!move_uploaded_file($archivo['tmp_name'], $tempBakFile)) {
            echo json_encode(['exito' => false, 'mensaje' => 'Error al procesar el archivo subido']);
            exit;
        }
        
        // Convertir .bak a .sql (cambio de extensión)
        if (!rename($tempBakFile, $tempSqlFile)) {
            @unlink($tempBakFile); // limpiar
            echo json_encode(['exito' => false, 'mensaje' => 'Error al procesar el archivo para restauración']);
            exit;
        }
        
        // Ejecutar restauración usando el modelo
        $dbModel = new DataBaseModel();
        $resultado = $dbModel->restaurarBackup($tempSqlFile);
        
        // Limpiar archivo temporal
        @unlink($tempSqlFile);
        
        echo json_encode($resultado);
        exit;
    }
    
    // Para otras acciones JSON (backup, etc.)
    $datos_recibidos = file_get_contents('php://input');
    $datos = json_decode($datos_recibidos, true);
    if (!$datos) {
        echo json_encode(['exito' => false, 'mensaje' => 'No se pudieron procesar los datos']);
        exit;
    }

    $accion = $datos['accion'];
    switch ($accion) {
        case 'crearBackup':
            // Crear carpeta de backups si no existe
            $backupsDir = __DIR__ . '/../../backups';
            if (!is_dir($backupsDir)) {
                if (!mkdir($backupsDir, 0755, true)) {
                    echo json_encode(['exito' => false, 'mensaje' => 'No se pudo crear la carpeta de backups']);
                    exit;
                }
            }

            // Nombre temporal en servidor para evitar colisiones
            $timestamp = date('Ymd_His');
            $serverFilename = "copiaSeguridad_{$timestamp}.bak";
            $serverPath = realpath($backupsDir) . DIRECTORY_SEPARATOR . $serverFilename;

            $dbModel = new DataBaseModel();
            $resultado = $dbModel->crearBackup($serverPath);

            if (!$resultado['exito']) {
                echo json_encode(['exito' => false, 'mensaje' => $resultado['mensaje']]);
                exit;
            }

            // Responder con el nombre del archivo en servidor. El frontend construye la URL de descarga
            echo json_encode([
                'exito' => true,
                'mensaje' => 'Backup generado',
                'file' => $serverFilename
            ]);
            break;
        default:
            echo json_encode(['exito' => false, 'mensaje' => 'Operación no reconocida']);
            break;
    }
}