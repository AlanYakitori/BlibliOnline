<?php
// ===================================
// PROCESAR PREFERENCIAS DE USUARIOS
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
    $datosRecibidos = file_get_contents('php://input');
    $datosPreferencias = json_decode($datosRecibidos, true);
    
    // Verificar que llegaron datos
    if (!$datosPreferencias) {
        echo json_encode([
            'exito' => false,
            'mensaje' => 'No se recibieron datos'
        ]);
        exit;
    }
    
    // Extraer datos recibidos
    $idUsuario = $datosPreferencias['idUsuario'];
    $tipoUsuario = $datosPreferencias['tipoUsuario'];
    $categoriasSeleccionadas = $datosPreferencias['categoriasSeleccionadas'];
    
    // Validar datos requeridos
    if (!$idUsuario || !$tipoUsuario || !$categoriasSeleccionadas) {
        echo json_encode([
            'exito' => false,
            'mensaje' => 'Datos incompletos'
        ]);
        exit;
    }
    
    try {
        // Iniciar transacción
        $conexion->beginTransaction();
        
        // Actualizar campo gusto del usuario
        $campoId = '';
        $tablaUsuario = '';
        
        if ($tipoUsuario === 'administrador') {
            $campoId = 'id_admin';
            $tablaUsuario = 'Administrador';
        } elseif ($tipoUsuario === 'docente') {
            $campoId = 'id_docente';
            $tablaUsuario = 'Docente';
        } elseif ($tipoUsuario === 'alumno') {
            $campoId = 'id_alumno';
            $tablaUsuario = 'Alumno';
        } else {
            throw new Exception('Tipo de usuario no válido');
        }
        
        // Actualizar campo gusto a true
        $consultaActualizarGusto = "UPDATE $tablaUsuario SET gusto = true WHERE $campoId = :idUsuario";
        $stmtActualizarGusto = $conexion->prepare($consultaActualizarGusto);
        $stmtActualizarGusto->bindParam(':idUsuario', $idUsuario);
        $stmtActualizarGusto->execute();
        
        // Insertar categorías del usuario
        $consultaInsertarCategoria = "INSERT INTO CategoriasUsuario (id_admin, id_docente, id_alumno, id_categoria) VALUES (?, ?, ?, ?)";
        $stmtInsertarCategoria = $conexion->prepare($consultaInsertarCategoria);
        
        foreach ($categoriasSeleccionadas as $idCategoria) {
            $idAdmin = ($tipoUsuario === 'administrador') ? $idUsuario : null;
            $idDocente = ($tipoUsuario === 'docente') ? $idUsuario : null;
            $idAlumno = ($tipoUsuario === 'alumno') ? $idUsuario : null;
            
            $stmtInsertarCategoria->execute([$idAdmin, $idDocente, $idAlumno, $idCategoria]);
        }
        
        // Obtener datos actualizados del usuario
        $consultaUsuario = "SELECT * FROM $tablaUsuario WHERE $campoId = :idUsuario";
        $stmtUsuario = $conexion->prepare($consultaUsuario);
        $stmtUsuario->bindParam(':idUsuario', $idUsuario);
        $stmtUsuario->execute();
        $datosUsuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);
        
        // Obtener categorías del usuario con nombres
        $consultaCategorias = "
            SELECT cu.id_categoria, c.nombre 
            FROM CategoriasUsuario cu 
            INNER JOIN Categoria c ON cu.id_categoria = c.id_categoria 
            WHERE ";
        
        if ($tipoUsuario === 'administrador') {
            $consultaCategorias .= "cu.id_admin = :idUsuario";
        } elseif ($tipoUsuario === 'docente') {
            $consultaCategorias .= "cu.id_docente = :idUsuario";
        } else {
            $consultaCategorias .= "cu.id_alumno = :idUsuario";
        }
        
        $stmtCategorias = $conexion->prepare($consultaCategorias);
        $stmtCategorias->bindParam(':idUsuario', $idUsuario);
        $stmtCategorias->execute();
        $categoriasUsuario = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);
        
        // Confirmar transacción
        $conexion->commit();
        
        // Eliminar contraseña antes de enviar datos
        unset($datosUsuario['contrasena']);
        
        // Agregar tipo de usuario a los datos
        $datosUsuario['tipoUsuario'] = $tipoUsuario;
        
        // Respuesta exitosa
        echo json_encode([
            'exito' => true,
            'mensaje' => 'Preferencias guardadas correctamente',
            'datosUsuario' => $datosUsuario,
            'categoriasUsuario' => $categoriasUsuario
        ]);
        
    } catch(Exception $error) {
        // Revertir transacción en caso de error
        $conexion->rollback();
        
        echo json_encode([
            'exito' => false,
            'mensaje' => 'Error al guardar preferencias: ' . $error->getMessage()
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
