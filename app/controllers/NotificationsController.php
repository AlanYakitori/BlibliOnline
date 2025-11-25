<?php
require_once __DIR__ . '/../models/NotificationsModel.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../config/session.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $datos_recibidos = file_get_contents('php://input');
    $datos_usuario = json_decode($datos_recibidos, true);
    
    $token_enviado = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    
    if (!$datos_usuario) {
        echo json_encode([
            'exito' => false,
            'mensaje' => 'No se pudieron procesar los datos del formulario'
        ]);
        exit;
    }
    
    // Verificar acción
    $accion = $datos_usuario['accion'];
    
    switch ($accion) {
        case 'obtenerRecursosPendientes':
            $id_docente = $datos_usuario['id_docente'];
            
            if (!$id_docente) {
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'ID del docente requerido'
                ]);
                break;
            }
            
            try {
                $notificaciones = new NotificationsModel();
                $resultado = $notificaciones->obtenerRecursosPendientes($conexion, $id_docente);
                echo json_encode($resultado);
            } catch (Exception $e) {
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error interno del servidor'
                ]);
            }
            break;
            
        case 'aprobarRecurso':
            $id_recurso = $datos_usuario['id_recurso'];
            $id_docente = $datos_usuario['id_docente'];
            
            if (!$id_recurso || !$id_docente) {
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'ID del recurso e ID del docente requeridos'
                ]);
                break;
            }
            
            try {
                $notificaciones = new NotificationsModel();
                $notificaciones->setIdRecurso($id_recurso);
                $notificaciones->setIdDocente($id_docente);
                
                $resultado = $notificaciones->aprobarRecurso($conexion);
                echo json_encode($resultado);
            } catch (Exception $e) {
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error interno del servidor al aprobar recurso'
                ]);
            }
            break;
            
        case 'rechazarRecurso':
            $id_recurso = $datos_usuario['id_recurso'];
            $id_docente = $datos_usuario['id_docente'];
            $motivo = $datos_usuario['motivo'] ?? '';
            
            if (!$id_recurso || !$id_docente || !$motivo) {
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'ID del recurso, ID del docente y motivo requeridos'
                ]);
                break;
            }
            
            try {
                $notificaciones = new NotificationsModel();
                $notificaciones->setIdRecurso($id_recurso);
                $notificaciones->setIdDocente($id_docente);
                $notificaciones->setMotivo($motivo);
                
                $resultado = $notificaciones->rechazarRecurso($conexion);
                echo json_encode($resultado);
            } catch (Exception $e) {
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error interno del servidor al rechazar recurso'
                ]);
            }
            break;
            
        case 'contarRecursosPendientes':
            $id_docente = $datos_usuario['id_docente'];
            
            if (!$id_docente) {
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'ID del docente requerido'
                ]);
                break;
            }
            
            try {
                $notificaciones = new NotificationsModel();
                $resultado = $notificaciones->contarRecursosPendientes($conexion, $id_docente);
                echo json_encode($resultado);
            } catch (Exception $e) {
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error interno del servidor'
                ]);
            }
            break;
            
        default:
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Operación no reconocida'
            ]);
            break;
    }
} else {
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Método no permitido'
    ]);
}
?>