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
    $nombre = $datos_usuario['nombre'];
    $apellidos = $datos_usuario['apellidos'];
    $telefono = $datos_usuario['telefono'];
    $correo = $datos_usuario['correo'];
    $contrasena = $datos_usuario['contrasena'];
    $tipo_usuario = $datos_usuario['tipoUsuario'];
    
    // Determinar en qué tabla guardar según el tipo de usuario
    if ($tipo_usuario === 'administrador') {
        $cargo = $datos_usuario['cargo'];
        $tabla = 'Administrador';
        $campos = 'nombre, apellidos, telefono, cargo, correo, contrasena';
        $valores = ':nombre, :apellidos, :telefono, :cargo, :correo, :contrasena';
        
    } elseif ($tipo_usuario === 'docente') {
        $especialidad = $datos_usuario['especialidad'];
        $tabla = 'Docente';
        $campos = 'nombre, apellidos, telefono, especialidad, correo, contrasena';
        $valores = ':nombre, :apellidos, :telefono, :especialidad, :correo, :contrasena';
        
    } elseif ($tipo_usuario === 'alumno') {
        $matricula = $datos_usuario['matricula'];
        $tabla = 'Alumno';
        $campos = 'nombre, apellidos, telefono, matricula, correo, contrasena';
        $valores = ':nombre, :apellidos, :telefono, :matricula, :correo, :contrasena';
        
    } else {
        echo json_encode([
            'exito' => false,
            'mensaje' => 'Tipo de usuario no válido'
        ]);
        exit;
    }
    
    try {
        // Preparar la consulta SQL
        $consulta = "INSERT INTO $tabla ($campos) VALUES ($valores)";
        $declaracion = $conexion->prepare($consulta);
        
        // Asignar valores a la consulta
        $declaracion->bindParam(':nombre', $nombre);
        $declaracion->bindParam(':apellidos', $apellidos);
        $declaracion->bindParam(':telefono', $telefono);
        $declaracion->bindParam(':correo', $correo);
        $declaracion->bindParam(':contrasena', $contrasena);
        
        // Asignar valor específico según tipo de usuario
        if ($tipo_usuario === 'administrador') {
            $declaracion->bindParam(':cargo', $cargo);
        } elseif ($tipo_usuario === 'docente') {
            $declaracion->bindParam(':especialidad', $especialidad);
        } elseif ($tipo_usuario === 'alumno') {
            $declaracion->bindParam(':matricula', $matricula);
        }
        
        // Ejecutar la consulta
        if ($declaracion->execute()) {
            // Éxito: usuario guardado
            echo json_encode([
                'exito' => true,
                'mensaje' => '¡Usuario registrado correctamente!'
            ]);
        } else {
            // Error al ejecutar
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al guardar el usuario'
            ]);
        }
        
    } catch(PDOException $error) {
        // Error de base de datos
        if ($error->getCode() == 23000) {
            echo json_encode([
                'exito' => false,
                'mensaje' => 'El correo ya está registrado'
            ]);
        } else {
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error en la base de datos: ' . $error->getMessage()
            ]);
        }
    }
    
} else {
    // No es una petición POST
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Método no permitido'
    ]);
}
?>