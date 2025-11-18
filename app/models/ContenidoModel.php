<?php
require_once __DIR__ . '/../../config/conexion.php';

class ContenidoModel {
    private $id_recurso;
    private $titulo;
    private $descripcion;
    private $archivo_url;
    private $imagen_url;
    private $calificacion;
    private $aprobado;
    private $id_categoria;
    private $id_usuario;

    public function __construct() {
        $this->id_recurso = 0;
        $this->titulo = '';
        $this->descripcion = '';
        $this->archivo_url = '';
        $this->imagen_url = '';
        $this->calificacion = 0;
        $this->aprobado = 0;
        $this->id_categoria = 0;
        $this->id_usuario = 0;
    }

    // Getters
    public function getIdRecurso() { return $this->id_recurso; }
    public function getTitulo() { return $this->titulo; }
    public function getDescripcion() { return $this->descripcion; }
    public function getArchivoUrl() { return $this->archivo_url; }
    public function getImagenUrl() { return $this->imagen_url; }
    public function getCalificacion() { return $this->calificacion; }
    public function getAprobado() { return $this->aprobado; }
    public function getIdCategoria() { return $this->id_categoria; }
    public function getIdUsuario() { return $this->id_usuario; }

    // Setters
    public function setIdRecurso($id_recurso) { $this->id_recurso = $id_recurso; }
    public function setTitulo($titulo) { $this->titulo = $titulo; }
    public function setDescripcion($descripcion) { $this->descripcion = $descripcion; }
    public function setArchivoUrl($archivo_url) { $this->archivo_url = $archivo_url; }
    public function setImagenUrl($imagen_url) { $this->imagen_url = $imagen_url; }
    public function setCalificacion($calificacion) { $this->calificacion = $calificacion; }
    public function setAprobado($aprobado) { $this->aprobado = $aprobado; }
    public function setIdCategoria($id_categoria) { $this->id_categoria = $id_categoria; }
    public function setIdUsuario($id_usuario) { $this->id_usuario = $id_usuario; }

    // Métodos de negocio
    public function agregarContenido($conexion) {
        try {
            $sql = "INSERT INTO Recurso (titulo, descripcion, archivo_url, imagen_url, calificacion, aprobado, id_categoria, id_usuario) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            
            $stmt->bindParam(1, $this->titulo, PDO::PARAM_STR);
            $stmt->bindParam(2, $this->descripcion, PDO::PARAM_STR);
            $stmt->bindParam(3, $this->archivo_url, PDO::PARAM_STR);
            
            // Si imagen_url está vacía, usar el valor por defecto de la base de datos
            if (empty($this->imagen_url)) {
                $stmt->bindParam(4, $this->imagen_url, PDO::PARAM_NULL);
            } else {
                $stmt->bindParam(4, $this->imagen_url, PDO::PARAM_STR);
            }
            
            $stmt->bindParam(5, $this->calificacion, PDO::PARAM_STR);
            
            if ($this->aprobado === null) {
                $stmt->bindParam(6, $this->aprobado, PDO::PARAM_NULL);
            } else {
                $stmt->bindParam(6, $this->aprobado, PDO::PARAM_INT);
            }
            
            $stmt->bindParam(7, $this->id_categoria, PDO::PARAM_INT);
            $stmt->bindParam(8, $this->id_usuario, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return [
                    'exito' => true,
                    'mensaje' => 'Contenido subido exitosamente'
                ];
            } else {
                return [
                    'exito' => false,
                    'mensaje' => 'Error al subir el contenido'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error al agregar contenido: " . $e->getMessage());
            return [
                'exito' => false,
                'mensaje' => 'Error en la base de datos al subir el contenido'
            ];
        }
    }

    public function consultarContenidoPorUsuario($conexion, $idUsuario) {
        try {
            $sql = "
                SELECT 
                    r.id_recurso,
                    r.titulo,
                    r.descripcion,
                    r.archivo_url,
                    r.imagen_url,
                    r.calificacion,
                    r.aprobado,
                    c.nombre as categoria
                FROM Recurso r
                INNER JOIN Categoria c ON r.id_categoria = c.id_categoria
                WHERE r.id_usuario = ?
                ORDER BY r.id_recurso DESC
            ";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(1, $idUsuario, PDO::PARAM_INT);
            $stmt->execute();
            
            $contenidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'exito' => true,
                'contenidos' => $contenidos
            ];
        } catch (PDOException $e) {
            error_log("Error al consultar contenido: " . $e->getMessage());
            return [
                'exito' => false,
                'mensaje' => 'Error al obtener el contenido'
            ];
        }
    }

    public function actualizarContenido($conexion, $idRecurso, $titulo, $descripcion, $archivoUrl, $imagen_url, $idCategoria, $aprobado) {
        try {
            // Verificar que el recurso existe
            $sqlVerificar = "SELECT COUNT(*) FROM Recurso WHERE id_recurso = ?";
            $stmtVerificar = $conexion->prepare($sqlVerificar);
            $stmtVerificar->bindParam(1, $idRecurso, PDO::PARAM_INT);
            $stmtVerificar->execute();
            
            if ($stmtVerificar->fetchColumn() == 0) {
                return [
                    'exito' => false,
                    'mensaje' => 'El contenido no existe'
                ];
            }

            // Actualizar el contenido
            $sql = "UPDATE Recurso SET titulo = ?, descripcion = ?, archivo_url = ?, imagen_url = ?, id_categoria = ?, aprobado = ? 
                    WHERE id_recurso = ?";
            $stmt = $conexion->prepare($sql);
            
            $stmt->bindParam(1, $titulo, PDO::PARAM_STR);
            $stmt->bindParam(2, $descripcion, PDO::PARAM_STR);
            $stmt->bindParam(3, $archivoUrl, PDO::PARAM_STR);
            
            // Si imagen_url está vacía, usar NULL para que la base de datos use el valor por defecto
            if (empty($imagen_url)) {
                $stmt->bindParam(4, $imagen_url, PDO::PARAM_NULL);
            } else {
                $stmt->bindParam(4, $imagen_url, PDO::PARAM_STR);
            }
            
            $stmt->bindParam(5, $idCategoria, PDO::PARAM_INT);
            
            if ($aprobado === null) {
                $stmt->bindParam(6, $aprobado, PDO::PARAM_NULL);
            } else {
                $stmt->bindParam(6, $aprobado, PDO::PARAM_INT);
            }
            
            $stmt->bindParam(7, $idRecurso, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return [
                    'exito' => true,
                    'mensaje' => 'Contenido actualizado exitosamente'
                ];
            } else {
                return [
                    'exito' => false,
                    'mensaje' => 'Error al actualizar el contenido'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error al actualizar contenido: " . $e->getMessage());
            return [
                'exito' => false,
                'mensaje' => 'Error en la base de datos al actualizar el contenido'
            ];
        }
    }

    public function eliminarContenido($conexion, $idRecurso) {
        try {
            // Verificar que el recurso existe
            $sqlVerificar = "SELECT COUNT(*) FROM Recurso WHERE id_recurso = ?";
            $stmtVerificar = $conexion->prepare($sqlVerificar);
            $stmtVerificar->bindParam(1, $idRecurso, PDO::PARAM_INT);
            $stmtVerificar->execute();
            
            if ($stmtVerificar->fetchColumn() == 0) {
                return [
                    'exito' => false,
                    'mensaje' => 'El contenido no existe'
                ];
            }

            // Eliminar el contenido
            $sql = "DELETE FROM Recurso WHERE id_recurso = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(1, $idRecurso, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return [
                    'exito' => true,
                    'mensaje' => 'Contenido eliminado exitosamente'
                ];
            } else {
                return [
                    'exito' => false,
                    'mensaje' => 'Error al eliminar el contenido'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error al eliminar contenido: " . $e->getMessage());
            return [
                'exito' => false,
                'mensaje' => 'Error en la base de datos al eliminar el contenido'
            ];
        }
    }
}
?>