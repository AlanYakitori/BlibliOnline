<?php
// ===================================
// PROCESAR INGRESO DE USUARIOS
// ===================================

// Incluir archivo de conexión
require_once 'conexion.php';

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
            'mensaje' => 'No se recibieron datos'
        ]);
        exit;
    }
    
    // Extraer datos del usuario
    $correo = $datos_usuario['correo'];
    $contrasena = $datos_usuario['contrasena'];
    $tipo_usuario = $datos_usuario['tipoUsuario'];
    
    // Determinar en qué tabla buscar según el tipo de usuario
    if ($tipo_usuario === 'administrador') {
        $tabla = 'Administrador';
    } elseif ($tipo_usuario === 'docente') {
        $tabla = 'Docente';
    } elseif ($tipo_usuario === 'alumno') {
        $tabla = 'Alumno';
    } else {
        echo json_encode([
            'exito' => false,
            'mensaje' => 'Tipo de usuario no válido'
        ]);
        exit;
    }
    
    // Buscar el correo y la contraseña en la tabla correspondiente y regresar toda su información
    try {
        $consulta = $conexion->prepare("SELECT * FROM $tabla WHERE correo = :correo");
        $consulta->bindParam(':correo', $correo);
        $consulta->execute();
        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
            if ($usuario["aceptado"] == true) {
                // Eliminar la contraseña del array antes de enviarlo
                unset($usuario['contrasena']);
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Ingreso exitoso',
                    'usuario' => $usuario
                ]);
            } else {
                // Credenciales inválidas
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Su peticion de crear cuenta aun no ha sido aceptada por algun administrador'
                ]);
            }
            
        } else {
            // Credenciales inválidas
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Correo o contraseña incorrectos'
            ]);
        }
    } catch(PDOException $error) {
        echo json_encode([
            'exito' => false,
            'mensaje' => 'Error en la base de datos: ' . $error->getMessage()
        ]);
        exit;
    }
    
} else {
    // No es una petición POST
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Método no permitido'
    ]);
}
?>