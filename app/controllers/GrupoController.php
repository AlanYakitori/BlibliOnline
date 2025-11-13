<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/GrupoModel.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../config/session.php';

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
        case 'crearGrupo':
            $nombre = $datos_usuario['nombre'];
            $clave = $datos_usuario['clave'];
            $docente = $datos_usuario['docente'];

            $grupo = new GrupoModel();

            $grupo->setNombre($nombre);
            $grupo->setClave($clave);
            $grupo->setDocente($docente);

            $resultado = $grupo->crearGrupo($conexion);
        
            echo json_encode($resultado);
            break;
            
        case 'actualizarGrupo':
            $idGrupo = $datos_usuario['idGrupo'];
            $nuevoNombre = $datos_usuario['nuevoNombre'];
            $grupo = new GrupoModel();
            $resultado = $grupo->actualizarGrupo($conexion, $idGrupo, $nuevoNombre);
            
            echo json_encode($resultado);
            break;
        
        case 'eliminarGrupo':
            $idGrupo = $datos_usuario['idGrupo'];
            $grupo = new GrupoModel();
            $resultado = $grupo->eliminarGrupo($conexion, $idGrupo);
            
            echo json_encode($resultado);
            break;
            
        case 'eliminarMiembro':
            $idMiembroGrupo = $datos_usuario['idMiembroGrupo'];
            $grupo = new GrupoModel();
            $resultado = $grupo->eliminarMiembro($conexion, $idMiembroGrupo);
            
            echo json_encode($resultado);
            break;
        
        case 'unirseGrupo':
            $codigoGrupo = $datos_usuario['codigoGrupo'];
            $idAlumno = $datos_usuario['idAlumno'];
            $grupo = new GrupoModel();
            $resultado = $grupo->unirseGrupo($conexion, $codigoGrupo, $idAlumno);
            
            echo json_encode($resultado);
            break;
            
        case 'verificarGrupoAlumno':
            $idAlumno = $datos_usuario['idAlumno'];
            $grupo = new GrupoModel();
            $resultado = $grupo->verificarGrupoAlumno($conexion, $idAlumno);
            
            echo json_encode($resultado);
            break;
            
        case 'verGrupoAlumno':
            $idAlumno = $datos_usuario['idAlumno'];
            $grupo = new GrupoModel();
            $resultado = $grupo->obtenerGrupoDeAlumno($conexion, $idAlumno);
            
            echo json_encode($resultado);
            break;
            
        case 'salirDeGrupo':
            $idAlumno = $datos_usuario['idAlumno'];
            $grupo = new GrupoModel();
            $resultado = $grupo->salirDeGrupo($conexion, $idAlumno);
            
            echo json_encode($resultado);
            break;
        
        case 'verGrupos':
            $idDocente = $datos_usuario['idDocente'];
            $grupo = new GrupoModel();
            $grupo->setDocente($idDocente);
            $resultado = $grupo->obtenerGruposPorDocente($conexion);
            
            echo json_encode($resultado);
            break;
        default:
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Operación no reconocida'
            ]);
            break;
    }
}