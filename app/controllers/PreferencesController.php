<?php
require_once __DIR__ . '/../models/PreferencesModel.php';
require_once __DIR__ . '/../../config/conexion.php';

// Configurar respuestas en formato JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Solo procesar si recibimos datos POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Leer los datos que envía JavaScript
    $datos_recibidos = file_get_contents('php://input');
    $datos_usuario = json_decode($datos_recibidos, true);
    
    // Verificar que llegaron datos
    if (!$datos_usuario) {
        echo json_encode([
            'exito' => false,
            'mensaje' => 'No se pudieron procesar los datos del formulario'
        ]);
        exit;
    }
    
    // Verificar accion
    $accion = $datos_usuario['accion'];

    switch ($accion) {
        case 'agregarPreferencias':
            // Extraer y validar datos del usuario
            $id = $datos_usuario['usuarioId'];
            $categoriasSeleccionadas = $datos_usuario['categoriasSeleccionadas'];

            try {
                // Crear objeto PreferencesModel
                $preferencias = new PreferencesModel();
                $preferencias->setId($id);
                $preferencias->setListaCategorias($categoriasSeleccionadas);

                // Ejecutar el guardado de preferencias
                $resultado = $preferencias->guardarPreferencia($conexion);

                // Retornar la respuesta del modelo
                echo json_encode($resultado);

            } catch (Exception $e) {
                error_log("Error en PreferencesController: " . $e->getMessage());
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error interno del servidor al guardar las preferencias'
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
}