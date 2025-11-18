<?php
              require_once __DIR__ . '/../models/ContenidoModel.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../config/session.php';

protegerPagina(['administrador', 'docente', 'alumno']);

class ContenidoController {
    
    public function manejarSolicitud() {
        // Configurar respuestas en formato JSON
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: Content-Type');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['accion'])) {
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Datos de entrada inválidos'
                ]);
                return;
            }

            $accion = $input['accion'];

            switch ($accion) {
                case 'agregarContenido':
                    $this->agregarContenido($input);
                    break;
                case 'consultarContenido':
                    $this->consultarContenido($input);
                    break;
                case 'actualizarContenido':
                    $this->actualizarContenido($input);
                    break;
                case 'eliminarContenido':
                    $this->eliminarContenido($input);
                    break;
                default:
                    echo json_encode([
                        'exito' => false,
                        'mensaje' => 'Acción no válida'
                    ]);
                    break;
            }
        }
    }

    private function agregarContenido($datos) {
        try {
            // Verificar datos requeridos
            $camposRequeridos = ['titulo', 'descripcion', 'archivo_url', 'id_categoria', 'id_usuario', 'tipo_usuario'];
            foreach ($camposRequeridos as $campo) {
                if (!isset($datos[$campo]) || empty(trim($datos[$campo]))) {
                    echo json_encode([
                        'exito' => false,
                        'mensaje' => "El campo $campo es obligatorio"
                    ]);
                    return;
                }
            }

            // Limpiar y validar datos
            $titulo = trim($datos['titulo']);
            $descripcion = trim($datos['descripcion']);
            $archivo_url = trim($datos['archivo_url']);
            $imagen_url = isset($datos['imagen_url']) ? trim($datos['imagen_url']) : '';
            $id_categoria = intval($datos['id_categoria']);
            $id_usuario = intval($datos['id_usuario']);
            $tipo_usuario = trim($datos['tipo_usuario']);

            // Validar longitudes
            if (strlen($titulo) > 100) {
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'El título no puede exceder 100 caracteres'
                ]);
                return;
            }

            if (strlen($descripcion) > 500) {
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'La descripción no puede exceder 500 caracteres'
                ]);
                return;
            }

            if (!filter_var($archivo_url, FILTER_VALIDATE_URL)) {
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'La URL proporcionada no es válida'
                ]);
                return;
            }

            // Configurar valores según el tipo de usuario
            // TODOS los contenidos empiezan con calificación 0
            $calificacion = 0.0;
            
            if ($tipo_usuario === 'administrador' || $tipo_usuario === 'docente') {
                $aprobado = 1;
            } else { // alumno
                $aprobado = null;
            }

            // Crear objeto del modelo
            $contenidoModel = new ContenidoModel();
            $contenidoModel->setTitulo($titulo);
            $contenidoModel->setDescripcion($descripcion);
            $contenidoModel->setArchivoUrl($archivo_url);
            $contenidoModel->setImagenUrl($imagen_url);
            $contenidoModel->setCalificacion($calificacion);
            $contenidoModel->setAprobado($aprobado);
            $contenidoModel->setIdCategoria($id_categoria);
            $contenidoModel->setIdUsuario($id_usuario);

            // Crear conexión y agregar contenido
            global $conexion;
            $resultado = $contenidoModel->agregarContenido($conexion);

            echo json_encode($resultado);

        } catch (Exception $e) {
            error_log("Error en agregarContenido: " . $e->getMessage());
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error interno del servidor'
            ]);
        }
    }

    private function consultarContenido($datos) {
        try {
            if (!isset($datos['id_usuario'])) {
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'ID de usuario requerido'
                ]);
                return;
            }

            $id_usuario = intval($datos['id_usuario']);
            
            $contenidoModel = new ContenidoModel();
            global $conexion;
            $resultado = $contenidoModel->consultarContenidoPorUsuario($conexion, $id_usuario);

            echo json_encode($resultado);

        } catch (Exception $e) {
            error_log("Error en consultarContenido: " . $e->getMessage());
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error interno del servidor'
            ]);
        }
    }

    private function actualizarContenido($datos) {
        try {
            // Verificar datos requeridos
            $camposRequeridos = ['id_recurso', 'titulo', 'descripcion', 'archivo_url', 'id_categoria', 'tipo_usuario'];
            foreach ($camposRequeridos as $campo) {
                if (!isset($datos[$campo])) {
                    echo json_encode([
                        'exito' => false,
                        'mensaje' => "El campo $campo es obligatorio"
                    ]);
                    return;
                }
            }

            // Limpiar y validar datos
            $id_recurso = intval($datos['id_recurso']);
            $titulo = trim($datos['titulo']);
            $descripcion = trim($datos['descripcion']);
            $archivo_url = trim($datos['archivo_url']);
            $imagen_url = isset($datos['imagen_url']) ? trim($datos['imagen_url']) : '';
            $id_categoria = intval($datos['id_categoria']);
            $tipo_usuario = trim($datos['tipo_usuario']);

            // Validaciones
            if (strlen($titulo) > 100) {
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'El título no puede exceder 100 caracteres'
                ]);
                return;
            }

            if (strlen($descripcion) > 500) {
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'La descripción no puede exceder 500 caracteres'
                ]);
                return;
            }

            if (!filter_var($archivo_url, FILTER_VALIDATE_URL)) {
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'La URL proporcionada no es válida'
                ]);
                return;
            }

            // Configurar aprobado según el tipo de usuario
            if ($tipo_usuario === 'administrador' || $tipo_usuario === 'docente') {
                $aprobado = 1;
            } else { // alumno
                $aprobado = null;
            }

            $contenidoModel = new ContenidoModel();
            global $conexion;
            $resultado = $contenidoModel->actualizarContenido($conexion, $id_recurso, $titulo, $descripcion, $archivo_url, $imagen_url, $id_categoria, $aprobado);

            echo json_encode($resultado);

        } catch (Exception $e) {
            error_log("Error en actualizarContenido: " . $e->getMessage());
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error interno del servidor'
            ]);
        }
    }

    private function eliminarContenido($datos) {
        try {
            if (!isset($datos['id_recurso'])) {
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'ID de recurso requerido'
                ]);
                return;
            }

            $id_recurso = intval($datos['id_recurso']);
            
            $contenidoModel = new ContenidoModel();
            global $conexion;
            $resultado = $contenidoModel->eliminarContenido($conexion, $id_recurso);

            echo json_encode($resultado);

        } catch (Exception $e) {
            error_log("Error en eliminarContenido: " . $e->getMessage());
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error interno del servidor'
            ]);
        }
    }
}

// Ejecutar el controlador
$controller = new ContenidoController();
$controller->manejarSolicitud();
?>