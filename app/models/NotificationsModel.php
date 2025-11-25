<?php
require_once __DIR__ . '/../../config/conexion.php';

class NotificationsModel {
    private $id_recurso;
    private $id_docente;
    private $motivo;

    public function __construct() {
        $this->id_recurso = '';
        $this->id_docente = '';
        $this->motivo = '';
    }

    // Getters
    public function getIdRecurso() { return $this->id_recurso; }
    public function getIdDocente() { return $this->id_docente; }
    public function getMotivo() { return $this->motivo; }

    // Setters
    public function setIdRecurso($id_recurso) { $this->id_recurso = $id_recurso; }
    public function setIdDocente($id_docente) { $this->id_docente = $id_docente; }
    public function setMotivo($motivo) { $this->motivo = $motivo; }

    /**
     * Obtiene los recursos pendientes de aprobación de alumnos del docente
     */
    public function obtenerRecursosPendientes($conexion, $id_docente) {
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
                    r.id_categoria,
                    c.nombre as nombre_categoria,
                    u.id_usuario,
                    u.nombre as nombre_alumno,
                    u.apellidos as apellidos_alumno,
                    u.dato as matricula_alumno,
                    g.nombre as nombre_grupo
                FROM Recurso r
                INNER JOIN Usuarios u ON r.id_usuario = u.id_usuario
                INNER JOIN Categoria c ON r.id_categoria = c.id_categoria
                INNER JOIN MiembrosGrupo mg ON u.id_usuario = mg.id_usuario
                INNER JOIN Grupos g ON mg.id_grupo = g.id_grupo
                WHERE g.docente = ? 
                  AND r.aprobado IS NULL
                  AND u.tipoUsuario = 'alumno'
                ORDER BY r.id_recurso DESC
            ";
            
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(1, $id_docente, PDO::PARAM_INT);
            $stmt->execute();
            
            return [
                'exito' => true,
                'recursos' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
            
        } catch (Exception $e) {
            error_log("Error en obtenerRecursosPendientes: " . $e->getMessage());
            return [
                'exito' => false,
                'mensaje' => 'Error al obtener recursos pendientes: ' . $e->getMessage(),
                'recursos' => []
            ];
        }
    }

    /**
     * Aprueba un recurso
     */
    public function aprobarRecurso($conexion) {
        try {
            $conexion->beginTransaction();
            
            $sql = "UPDATE Recurso SET aprobado = 1 WHERE id_recurso = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(1, $this->id_recurso, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $conexion->commit();
                return [
                    'exito' => true,
                    'mensaje' => 'Recurso aprobado exitosamente'
                ];
            } else {
                throw new Exception("Error al aprobar el recurso");
            }
            
        } catch (Exception $e) {
            $conexion->rollBack();
            error_log("Error en aprobarRecurso: " . $e->getMessage());
            return [
                'exito' => false,
                'mensaje' => 'Error al aprobar el recurso: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Rechaza un recurso y guarda el motivo
     */
    public function rechazarRecurso($conexion) {
        try {
            $conexion->beginTransaction();
            
            // Actualizar el recurso como rechazado
            $sqlRecurso = "UPDATE Recurso SET aprobado = 0 WHERE id_recurso = ?";
            $stmtRecurso = $conexion->prepare($sqlRecurso);
            $stmtRecurso->bindParam(1, $this->id_recurso, PDO::PARAM_INT);
            
            if (!$stmtRecurso->execute()) {
                throw new Exception("Error al rechazar el recurso");
            }
            
            // Insertar el registro en la tabla de rechazos
            $sqlRechazo = "INSERT INTO ListaRechazos (id_usuario, id_recurso, motivo) VALUES (?, ?, ?)";
            $stmtRechazo = $conexion->prepare($sqlRechazo);
            $stmtRechazo->bindParam(1, $this->id_docente, PDO::PARAM_INT);
            $stmtRechazo->bindParam(2, $this->id_recurso, PDO::PARAM_INT);
            $stmtRechazo->bindParam(3, $this->motivo, PDO::PARAM_STR);
            
            if (!$stmtRechazo->execute()) {
                throw new Exception("Error al guardar el motivo del rechazo");
            }
            
            $conexion->commit();
            return [
                'exito' => true,
                'mensaje' => 'Recurso rechazado exitosamente'
            ];
            
        } catch (Exception $e) {
            $conexion->rollBack();
            error_log("Error en rechazarRecurso: " . $e->getMessage());
            return [
                'exito' => false,
                'mensaje' => 'Error al rechazar el recurso: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene el conteo de recursos pendientes para un docente
     */
    public function contarRecursosPendientes($conexion, $id_docente) {
        try {
            $sql = "
                SELECT COUNT(*) as total
                FROM Recurso r
                INNER JOIN Usuarios u ON r.id_usuario = u.id_usuario
                INNER JOIN MiembrosGrupo mg ON u.id_usuario = mg.id_usuario
                INNER JOIN Grupos g ON mg.id_grupo = g.id_grupo
                WHERE g.docente = ? 
                  AND r.aprobado IS NULL
                  AND u.tipoUsuario = 'alumno'
            ";
            
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(1, $id_docente, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return [
                'exito' => true,
                'total' => $result['total']
            ];
            
        } catch (Exception $e) {
            error_log("Error en contarRecursosPendientes: " . $e->getMessage());
            return [
                'exito' => false,
                'total' => 0
            ];
        }
    }
}
?>