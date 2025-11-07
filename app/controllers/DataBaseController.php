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

// Para otras acciones esperamos POST con JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Leer los datos que envía JavaScript
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