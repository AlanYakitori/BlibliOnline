<?php
require_once __DIR__ . '/../../config/session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['exito' => false, 'mensaje' => 'Método no permitido']);
    exit;
}

$datos = json_decode(file_get_contents('php://input'), true);
$token = $datos['csrf_token'] ?? '';

if (!verificarCSRFToken($token)) {
    echo json_encode(['exito' => false, 'mensaje' => 'Token CSRF inválido']);
    exit;
}

// Cerrar sesión
cerrarSesion();

echo json_encode(['exito' => true, 'mensaje' => 'Sesión cerrada correctamente']);
exit;

?>
