<?php
// ===================================
// PROCESAR REGISTRO DE USUARIOS
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
    
    $contrasena = $datos_usuario['contrasena'];
    $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
    $correo = $datos_usuario['correo'];
    $tipo_usuario = $datos_usuario['tipoUsuario'];

    
    // Determinar en qué tabla guardar según el tipo de usuario
    if ($tipo_usuario === 'administrador') {
        $tabla = 'Administrador';
        $valores = ':correo, :contrasena';
        
    } elseif ($tipo_usuario === 'docente') {
        $especialidad = $datos_usuario['especialidad'];
        $tabla = 'Docente';
        $valores = ':nombre, :apellidos, :telefono, :especialidad, :correo, :contrasena';
        
    } elseif ($tipo_usuario === 'alumno') {
        $matricula = $datos_usuario['matricula'];
        $tabla = 'Alumno';
        $valores = ':nombre, :apellidos, :telefono, :matricula, :correo, :contrasena';
        
    } else {
        echo json_encode([
            'exito' => false,
            'mensaje' => 'Tipo de usuario no válido'
        ]);
        exit;
    }

    try {
        // Verificar si el correo ya existe en la tabla correspondiente
        $consulta_verificacion = "SELECT COUNT(*) FROM $tabla WHERE correo = :correo";
        $stmt_verificacion = $conexion->prepare($consulta_verificacion);
        $stmt_verificacion->bindParam(':correo', $correo);
        $stmt_verificacion->execute();
        if ($stmt_verificacion->fetchColumn() <= 0) {
            echo json_encode([
                'exito' => false,
                'mensaje' => 'El correo aun no está registrado'
            ]);
            exit;
        }
    } catch (Exception $e) {
        echo json_encode([
            'exito' => false,
            'mensaje' => 'Error en la base de datos: ' . $e->getMessage()
        ]);
        exit;
    }
    
    
    
    try {
        // Preparar la consulta SQL
        $stmt = $conexion->prepare("UPDATE $tabla SET contrasena = :contrasena WHERE correo = :correo");
        $stmt->bindParam(':contrasena', $contrasena_hash);
        $stmt->bindParam(':correo', $correo);
        

        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Éxito: usuario guardado
            echo json_encode([
                'exito' => true,
                'mensaje' => '¡Usuario actualizado correctamente!'
            ]);
        } else {
            // Error al ejecutar
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al guardar el usuario'
            ]);
        }
        
    } catch(PDOException $error) {
        echo json_encode([
            'exito' => false,
            'mensaje' => 'Error en la base de datos: ' . $error->getMessage()
        ]);
    }
    
} else {
    // No es una petición POST
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Método no permitido'
    ]);
}
?>