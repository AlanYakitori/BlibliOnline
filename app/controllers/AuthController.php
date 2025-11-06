<?php
require_once __DIR__ . '/../models/UserModel.php';
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
            $usuario = new UserModel ();  
            $usuario->setNombre($nombre);
            $usuario->setApellidos($apellidos);
            $usuario->setTipoUsuario($tipoUsuario);
            $usuario->setTelefono($telefono);
            $usuario->setDato($dato);
            $usuario->setCorreo($correo);
            $usuario->setContrasena($contrasenaHash);
            $usuario->setAceptado($aceptado);
            $usuario->setGusto($gusto);
            $usuario->setGenero($genero);
            $usuario->setFechaNacimiento($fechaNacimiento);
            
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
        case 'ingresar':
            // Extraer datos del usuario
            $correo = $datos_usuario['correo'];
            $contrasena = $datos_usuario['contrasena'];
            $tipoUsuario = $datos_usuario['tipoUsuario'];
            
            // Crear objeto UserModel
            $usuario = new UserModel();
            
            // Validar credenciales
            $datosUsuario = $usuario->validarCredenciales($conexion, $correo, $contrasena);
            
            // Si no se encontró el usuario o la contraseña es incorrecta
            if ($datosUsuario === null || $datosUsuario === false) {
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Correo o contraseña incorrectos'
                ]);
                break;
            }
            
            // Verificar que el tipo de usuario coincida
            if ($datosUsuario['tipoUsuario'] !== $tipoUsuario) {
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Correo o contraseña incorrectos'
                ]);
                break;
            }
            
            // Verificar si el usuario ha sido aceptado
            if ($datosUsuario['aceptado'] == 0) {
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Su petición de creación de usuario no ha sido aceptada'
                ]);
                break;
            }
            
            // Si todo está bien, retornar los datos del usuario
            // Iniciar sesión en el servidor para proteger accesos a dashboards
            $usuarioSession = [
                'id' => $datosUsuario['id_usuario'],
                'nombre' => $datosUsuario['nombre'],
                'apellidos' => $datosUsuario['apellidos'],
                'tipoUsuario' => $datosUsuario['tipoUsuario'],
                'telefono' => $datosUsuario['telefono'],
                'dato' => $datosUsuario['dato'],
                'correo' => $datosUsuario['correo'],
                'aceptado' => $datosUsuario['aceptado'],
                'gusto' => $datosUsuario['gusto'],
                'genero' => $datosUsuario['genero'],
                'fechaNacimiento' => $datosUsuario['fechaNacimiento']
            ];

            iniciarSesionUsuario($usuarioSession);

            echo json_encode([
                'exito' => true,
                'mensaje' => 'Inicio de sesión exitoso',
                'usuario' => $usuarioSession
            ]);
            break;
        case 'cambiarContrasenia':
            // Extraer datos del usuario
            
            $nuevaContrasena = $datos_usuario['contrasenia'];
            $nuevaContrasenaHash = password_hash($nuevaContrasena, PASSWORD_DEFAULT);
            $correo = $datos_usuario['correo'];
            
            // Crear objeto UserModel
            $usuario = new UserModel();
            
            $usuario->setCorreo($correo);
            $usuario->setContrasena($nuevaContrasenaHash);

            // Ejecutar el método cambiarContrasenia
            $result = $usuario->actualizarContrasena($conexion);

            // Enviar respuesta basada en el resultado
            if ($result) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Contraseña cambiada exitosamente. Redirigiendo al inicio de sesión...'
                ]);
            } else {
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Ocurrió un error al cambiar la contraseña. Inténtalo de nuevo.'
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