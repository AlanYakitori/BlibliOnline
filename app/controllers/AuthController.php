<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/ContenidoModel.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../config/session.php'; 

header('Content-Type: application/json');

header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: POST');
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
    
    // Verificar accion
    $accion = $datos_usuario['accion'];

    switch ($accion) {
        case 'registrar':
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
            
            if ($usuario->correoExiste($conexion)) {
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Este correo electrónico ya está registrado. Intenta con otro correo.'
                ]);
                break;
            }
            
            $resultado = $usuario->registrarUsuario($conexion);
            
            if ($resultado) {
                echo json_encode(['exito' => true, 'mensaje' => 'Registro exitoso. Redirigiendo...']);
            } else {
                echo json_encode(['exito' => false, 'mensaje' => 'Ocurrió un error al guardar tus datos.']);
            }
            break;
        
        case 'ingresar':
            $correo = $datos_usuario['correo'];
            $contrasena = $datos_usuario['contrasena'];
            $tipoUsuario = $datos_usuario['tipoUsuario'];
            
            $usuario = new UserModel();
            $datosUsuario = $usuario->validarCredenciales($conexion, $correo, $contrasena);
            
            if ($datosUsuario === null || $datosUsuario === false) {
                echo json_encode(['exito' => false, 'mensaje' => 'Correo o contraseña incorrectos']);
                break;
            }
            
            if ($datosUsuario['tipoUsuario'] !== $tipoUsuario) {
                echo json_encode(['exito' => false, 'mensaje' => 'Correo o contraseña incorrectos']);
                break;
            }
            
            if ($datosUsuario['aceptado'] == 0) {
                echo json_encode(['exito' => false, 'mensaje' => 'Su petición de creación de usuario no ha sido aceptada']);
                break;
            }
            
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
            $nuevaContrasena = $datos_usuario['contrasenia'];
            $nuevaContrasenaHash = password_hash($nuevaContrasena, PASSWORD_DEFAULT);
            $correo = $datos_usuario['correo'];
            
            $usuario = new UserModel();
            $usuario->setCorreo($correo);
            $usuario->setContrasena($nuevaContrasenaHash);
            $result = $usuario->actualizarContrasena($conexion);

            if ($result) {
                echo json_encode(['exito' => true, 'mensaje' => 'Contraseña cambiada exitosamente.']);
            } else {
                echo json_encode(['exito' => false, 'mensaje' => 'Ocurrió un error al cambiar la contraseña.']);
            }
            break;
        
        case 'consultarUsuarios':
            try {
                $modelo = new UserModel();
                $listaUsuarios = $modelo->consultarTodos($conexion); 
                echo json_encode(['exito' => true, 'usuarios' => $listaUsuarios]);
            } catch (Exception $e) {
                echo json_encode(['exito' => false, 'mensaje' => 'Error del servidor: ' . $e->getMessage()]);
            }
            break;
        
        case 'consultarUsuarioUnico':
            try {
                $id = $datos_usuario['id_usuario'] ?? null;
                if (!$id) {
                    throw new Exception('ID de usuario no proporcionado');
                }
                $modelo = new UserModel();
                $usuario = $modelo->consultarPorId($conexion, $id); 
                echo json_encode(['exito' => true, 'usuario' => $usuario]);
            } catch (Exception $e) {
                echo json_encode(['exito' => false, 'mensaje' => $e->getMessage()]);
            }
            break;
        
        case 'eliminarUsuario': 
            try {
                $id_usuario = $datos_usuario['id_usuario'] ?? null;
                if (!$id_usuario) {
                    throw new Exception('No se proporcionó un ID de usuario.');
                }
                $modelo = new UserModel();
                $exito = $modelo->eliminarPorId($conexion, $id_usuario); 
                if ($exito) {
                    echo json_encode(['exito' => true, 'mensaje' => 'Usuario eliminado correctamente']);
                } else {
                    throw new Exception('No se pudo eliminar el usuario.');
                }
            } catch (Exception $e) {
                echo json_encode(['exito' => false, 'mensaje' => $e->getMessage()]);
            }
            break;
        
        case 'actualizarUsuario':
            try {
                $modelo = new UserModel();
                
                $modelo->setId($datos_usuario['id_usuario']);
                $modelo->setNombre($datos_usuario['nombre']);
                $modelo->setApellidos($datos_usuario['apellidos']);
                $modelo->setTelefono($datos_usuario['telefono']);
                $modelo->setCorreo($datos_usuario['correo']);

                if (isset($datos_usuario['contrasena']) && !empty($datos_usuario['contrasena'])) {
                    $hash = password_hash($datos_usuario['contrasena'], PASSWORD_DEFAULT);
                    $modelo->setContrasena($hash);
                }

                $resultado = $modelo->actualizarPerfilUsuario($conexion); 

                if ($resultado) {
                    echo json_encode([
                        'exito' => true, 
                        'mensaje' => 'Usuario actualizado correctamente'
                    ]);
                } else {
                    throw new Exception('No se pudo actualizar el usuario en la base de datos.');
                }

            } catch (Exception $e) {
                echo json_encode([
                    'exito' => false, 
                    'mensaje' => $e->getMessage()
                ]);
            }
            break;
        
        case 'obtenerNoticiasDestacadas':
            try {
                $id_usuario = $_SESSION['usuario']['id'] ?? null;
                $modelo = new UserModel();
                $noticias = $modelo->consultarNoticiasDestacadas($conexion, $id_usuario);

                echo json_encode(
                    ['exito' => true, 
                    'noticias' => $noticias
                ]);
                
            } catch (Exception $e) {
                echo json_encode(['exito' => false, 'mensaje' => $e->getMessage()]);
            }             
            break;
        case 'obtenerMisFavoritos':
            try {
                $id_usuario = $_SESSION['usuario']['id'] ?? null;
                if (!$id_usuario) {
                    throw new Exception('Usuario no autenticado');
                }
                
                $modelo = new UserModel();
                $favoritos = $modelo->consultarFavoritosPorUsuario($conexion, $id_usuario);
                
                echo json_encode([
                    'exito' => true, 
                    'favoritos' => $favoritos
                ]);

            } catch (Exception $e) {
                echo json_encode(['exito' => false]);
            }
            break;
        case 'marcarFavorito':
            try {
                $id_usuario = $_SESSION['usuario']['id'] ?? null;
                if (!$id_usuario) {
                    throw new Exception('Usuario no autenticado');
                }

                $id_recurso = $datos_usuario['id_recurso'] ?? null;
                $es_favorito = $datos_usuario['es_favorito'] ?? false; // bool

                if (!$id_recurso) {
                    throw new Exception('ID de recurso no proporcionado');
                }

                $modelo = new UserModel();
                $exito = $modelo->gestionarFavorito($conexion, $id_usuario, $id_recurso, $es_favorito);

                if ($exito) {
                    echo json_encode(['exito' => true, 'mensaje' => 'Favorito actualizado']);
                } else {
                    throw new Exception('No se pudo actualizar el favorito');
                }

            } catch (Exception $e) {
                echo json_encode(['exito' => false, 'mensaje' => $e->getMessage()]);
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