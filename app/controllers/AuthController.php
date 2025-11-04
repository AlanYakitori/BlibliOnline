<?php
require_once __DIR__ . '/../models/UserModel.php';
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
        case 'registrar':
            // Extraer datos del usuario
            $nombre = $datos_usuario['nombre'];
            $apellidos = $datos_usuario['apellidos'];
            $tipoUsuario = $datos_usuario['tipoUsuario'];
            $telefono = $datos_usuario['telefono'];
            $dato = $datos_usuario['dato'];
            $correo = $datos_usuario['correo'];
            $contrasena = $datos_usuario['contrasena'];
            $contrasenaHash = password_hash($contrasena, PASSWORD_DEFAULT);
            $aceptado = $datos_usuario['aceptado'];
            $gusto = $datos_usuario['gusto'];
            $genero = $datos_usuario['genero'];
            $fechaNacimiento = $datos_usuario['fechaNacimiento'];
            

            // Crear objeto UserModel
            $usuario = new UserModel(
                $nombre,
                $apellidos,
                $tipoUsuario,
                $telefono,
                $dato,
                $correo,
                $contrasenaHash,
                $aceptado,
                $gusto,
                $genero,
                $fechaNacimiento
            );  
            
            // Verificar si el correo ya existe antes de registrar
            if ($usuario->correoExiste($conexion)) {
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Este correo electrónico ya está registrado. Intenta con otro correo.'
                ]);
                break;
            }
            
            // Ejecutar el método registrarUsuario
            $resultado = $usuario->registrarUsuario($conexion);
            
            // Enviar respuesta basada en el resultado
            if ($resultado) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Registro exitoso. Redirigiendo al inicio de sesión...'
                ]);
            } else {
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Ocurrió un error al guardar tus datos. Inténtalo de nuevo.'
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