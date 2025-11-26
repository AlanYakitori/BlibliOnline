<?php
require_once __DIR__ . '/../../config/conexion.php';

class NotificationsModel {
    private $id_recurso;
    private $id_docente;
    private $motivo;
    private $id_usuario;
    private $id_administrador;

    public function __construct() {
        $this->id_recurso = '';
        $this->id_docente = '';
        $this->motivo = '';
        $this->id_usuario = '';
        $this->id_administrador = '';
    }

    // Getters
    public function getIdRecurso() { return $this->id_recurso; }
    public function getIdDocente() { return $this->id_docente; }
    public function getMotivo() { return $this->motivo; }
    public function getIdUsuario() { return $this->id_usuario; }
    public function getIdAdministrador() { return $this->id_administrador; }

    // Setters
    public function setIdRecurso($id_recurso) { $this->id_recurso = $id_recurso; }
    public function setIdDocente($id_docente) { $this->id_docente = $id_docente; }
    public function setMotivo($motivo) { $this->motivo = $motivo; }
    public function setIdUsuario($id_usuario) { $this->id_usuario = $id_usuario; }
    public function setIdAdministrador($id_administrador) { $this->id_administrador = $id_administrador; }

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

    /**
     * Obtener recursos subidos por un alumno con su estado de aprobación
     * @param int $id_alumno ID del alumno
     * @return array Resultado con recursos y sus estados
     */
    public function obtenerRecursosAlumno($conexion, $id_alumno) {
        try {
            $consulta = "
                SELECT 
                    r.id_recurso,
                    r.titulo,
                    r.descripcion,
                    r.archivo_url,
                    r.imagen_url,
                    r.aprobado,
                    r.calificacion,
                    c.nombre as categoria,
                    lr.motivo as motivo_rechazo
                FROM Recurso r
                LEFT JOIN Categoria c ON r.id_categoria = c.id_categoria
                LEFT JOIN ListaRechazos lr ON r.id_recurso = lr.id_recurso
                WHERE r.id_usuario = ?
                ORDER BY r.id_recurso DESC
            ";
            
            $stmt = $conexion->prepare($consulta);
            $stmt->bindParam(1, $id_alumno, PDO::PARAM_INT);
            $stmt->execute();
            
            $recursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'exito' => true,
                'recursos' => $recursos
            ];
            
        } catch (Exception $e) {
            error_log("Error en obtenerRecursosAlumno: " . $e->getMessage());
            return [
                'exito' => false,
                'recursos' => []
            ];
        }
    }

    /**
     * Contar notificaciones pendientes para un alumno
     * @param int $id_alumno ID del alumno
     * @return array Resultado con el conteo
     */
    public function contarNotificacionesAlumno($conexion, $id_alumno) {
        try {
            $consulta = "
                SELECT COUNT(*) as total
                FROM Recurso r
                WHERE r.id_usuario = ? 
                AND r.aprobado IS NOT NULL
            ";
            
            $stmt = $conexion->prepare($consulta);
            $stmt->bindParam(1, $id_alumno, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return [
                'exito' => true,
                'total' => $result['total']
            ];
            
        } catch (Exception $e) {
            error_log("Error en contarNotificacionesAlumno: " . $e->getMessage());
            return [
                'exito' => false,
                'total' => 0
            ];
        }
    }

    /**
     * Obtener usuarios pendientes de aprobación para administradores
     * @return array Resultado con usuarios pendientes
     */
    public function obtenerUsuariosPendientes($conexion) {
        try {
            $consulta = "
                SELECT 
                    u.id_usuario,
                    u.nombre,
                    u.apellidos,
                    u.correo,
                    u.tipoUsuario,
                    u.fechaNacimiento,
                    u.aceptado
                FROM Usuarios u
                WHERE u.aceptado = 0
                ORDER BY u.id_usuario ASC
            ";
            
            $stmt = $conexion->prepare($consulta);
            $stmt->execute();
            
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'exito' => true,
                'usuarios' => $usuarios
            ];
            
        } catch (Exception $e) {
            error_log("Error en obtenerUsuariosPendientes: " . $e->getMessage());
            return [
                'exito' => false,
                'usuarios' => []
            ];
        }
    }

    /**
     * Aprobar un usuario
     * @param int $id_usuario ID del usuario a aprobar
     * @return array Resultado de la operación
     */
    public function aprobarUsuario($conexion, $id_usuario) {
        try {
            $conexion->beginTransaction();
            
            // Actualizar estado del usuario
            $consulta = "UPDATE Usuarios SET aceptado = 1 WHERE id_usuario = ?";
            $stmt = $conexion->prepare($consulta);
            $stmt->bindParam(1, $id_usuario, PDO::PARAM_INT);
            $stmt->execute();
            
            $conexion->commit();
            
            return [
                'exito' => true,
                'mensaje' => 'Usuario aprobado correctamente'
            ];
            
        } catch (Exception $e) {
            $conexion->rollback();
            error_log("Error en aprobarUsuario: " . $e->getMessage());
            return [
                'exito' => false,
                'mensaje' => 'Error al aprobar el usuario'
            ];
        }
    }

    /**
     * Rechazar un usuario y enviar notificación por email
     * @param int $id_usuario ID del usuario a rechazar
     * @param string $motivo Motivo del rechazo
     * @return array Resultado de la operación
     */
    public function rechazarUsuario($conexion, $id_usuario, $motivo) {
        try {
            $conexion->beginTransaction();
            
            // Obtener datos del usuario antes de eliminarlo
            $consultaUsuario = "SELECT nombre, correo FROM Usuarios WHERE id_usuario = ?";
            $stmtUsuario = $conexion->prepare($consultaUsuario);
            $stmtUsuario->bindParam(1, $id_usuario, PDO::PARAM_INT);
            $stmtUsuario->execute();
            $usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);
            
            if (!$usuario) {
                throw new Exception("Usuario no encontrado");
            }
            
            // Eliminar usuario de la base de datos
            $consulta = "DELETE FROM Usuarios WHERE id_usuario = ?";
            $stmt = $conexion->prepare($consulta);
            $stmt->bindParam(1, $id_usuario, PDO::PARAM_INT);
            $stmt->execute();
            
            $conexion->commit();
            
            // Enviar email de notificación de rechazo
            require_once __DIR__ . '/../services/EmailServiceClass.php';
            $emailService = new EmailService();
            
            $asunto = "Registro rechazado - BiblioOnline";
            $mensaje = "
                <h2>Registro Rechazado</h2>
                <p>Estimado/a {$usuario['nombre']},</p>
                <p>Lamentamos informarle que su registro en BiblioOnline ha sido rechazado.</p>
                <p><strong>Motivo:</strong> {$motivo}</p>
                <p>Si tiene alguna pregunta, por favor contacte al administrador del sistema.</p>
                <br>
                <p>Saludos cordiales,<br>
                Equipo de BiblioOnline</p>
            ";
            
            $resultadoEmail = $emailService->enviarEmail($usuario['correo'], $asunto, $mensaje);
            
            return [
                'exito' => true,
                'mensaje' => 'Usuario rechazado y notificado correctamente'
            ];
            
        } catch (Exception $e) {
            $conexion->rollback();
            error_log("Error en rechazarUsuario: " . $e->getMessage());
            return [
                'exito' => false,
                'mensaje' => 'Error al rechazar el usuario: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Contar usuarios pendientes de aprobación
     * @return array Resultado con el conteo
     */
    public function contarUsuariosPendientes($conexion) {
        try {
            $consulta = "SELECT COUNT(*) as total FROM Usuarios WHERE aceptado = 0";
            $stmt = $conexion->prepare($consulta);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return [
                'exito' => true,
                'total' => $result['total']
            ];
            
        } catch (Exception $e) {
            error_log("Error en contarUsuariosPendientes: " . $e->getMessage());
            return [
                'exito' => false,
                'total' => 0
            ];
        }
    }
}
?>