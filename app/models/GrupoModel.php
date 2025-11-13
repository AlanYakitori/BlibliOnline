<?php
require_once __DIR__ . '/../../config/conexion.php';

class GrupoModel {
    private $id;
    private $nombre;
    private $clave;
    private $docente;
    private $mienbros;

    public function __construct() {
        $this->id = '';
        $this->nombre = '';
        $this->clave = '';
        $this->docente = '';
        $this->mienbros = [];
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNombre() { return $this->nombre; }
    public function getClave() { return $this->clave; }
    public function getDocente() { return $this->docente; } 
    public function getMienbros() { return $this->mienbros; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function setClave($clave) { $this->clave = $clave; }
    public function setDocente($docente) { $this->docente = $docente; }
    public function setMienbros($mienbros) { $this->mienbros = $mienbros; }

    public function crearGrupo($conexion) {
        try {
            // Validar que el nombre no esté vacío
            if (empty(trim($this->nombre))) {
                return [
                    'exito' => false,
                    'mensaje' => 'El nombre del grupo es obligatorio'
                ];
            }

            // Verificar si ya existe un grupo con el mismo nombre para este docente
            $sqlVerificar = "SELECT COUNT(*) FROM Grupos WHERE nombre = ? AND docente = ?";
            $stmtVerificar = $conexion->prepare($sqlVerificar);
            $stmtVerificar->bindParam(1, $this->nombre, PDO::PARAM_STR);
            $stmtVerificar->bindParam(2, $this->docente, PDO::PARAM_INT);
            $stmtVerificar->execute();
            
            if ($stmtVerificar->fetchColumn() > 0) {
                return [
                    'exito' => false,
                    'mensaje' => 'Ya tienes un grupo con ese nombre'
                ];
            }

            $sql = "INSERT INTO Grupos (nombre, clave, docente) VALUES (?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(1, $this->nombre, PDO::PARAM_STR);
            $stmt->bindParam(2, $this->clave, PDO::PARAM_STR);
            $stmt->bindParam(3, $this->docente, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return [
                    'exito' => true,
                    'mensaje' => 'Grupo creado exitosamente'
                ];
            } else {
                return [
                    'exito' => false,
                    'mensaje' => 'Error al crear el grupo'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error al crear grupo: " . $e->getMessage());
            return [
                'exito' => false,
                'mensaje' => 'Error en la base de datos al crear el grupo'
            ];
        }
    }

    public function obtenerGruposPorDocente($conexion) {
        try {
            // Obtener los grupos del docente
            $sql = "SELECT * FROM Grupos WHERE docente = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(1, $this->docente, PDO::PARAM_INT);
            $stmt->execute();
            $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Para cada grupo, obtener sus miembros
            foreach ($grupos as &$grupo) {
                $sqlMiembros = "
                    SELECT 
                        u.id_usuario,
                        u.nombre,
                        u.apellidos,
                        u.correo,
                        mg.id_miembro_grupo
                    FROM MiembrosGrupo mg
                    INNER JOIN Usuarios u ON mg.id_usuario = u.id_usuario
                    WHERE mg.id_grupo = ?
                ";
                $stmtMiembros = $conexion->prepare($sqlMiembros);
                $stmtMiembros->bindParam(1, $grupo['id_grupo'], PDO::PARAM_INT);
                $stmtMiembros->execute();
                $miembros = $stmtMiembros->fetchAll(PDO::FETCH_ASSOC);
                
                // Agregar los miembros al grupo
                $grupo['miembros'] = $miembros;
                $grupo['total_miembros'] = count($miembros);
            }

            return [
                'exito' => true,
                'grupos' => $grupos
            ];
        } catch (PDOException $e) {
            error_log("Error al obtener grupos con miembros: " . $e->getMessage());
            return [
                'exito' => false,
                'mensaje' => 'Error al obtener los grupos y sus miembros'
            ];
        }
    }

    public function eliminarGrupo($conexion, $idGrupo) {
        try {
            // Verificar que el grupo existe
            $sqlVerificar = "SELECT COUNT(*) FROM Grupos WHERE id_grupo = ?";
            $stmtVerificar = $conexion->prepare($sqlVerificar);
            $stmtVerificar->bindParam(1, $idGrupo, PDO::PARAM_INT);
            $stmtVerificar->execute();
            
            if ($stmtVerificar->fetchColumn() == 0) {
                return [
                    'exito' => false,
                    'mensaje' => 'El grupo no existe'
                ];
            }

            // Iniciar transacción para eliminación segura
            $conexion->beginTransaction();

            // Primero eliminar todos los miembros del grupo
            $sqlEliminarMiembros = "DELETE FROM MiembrosGrupo WHERE id_grupo = ?";
            $stmtEliminarMiembros = $conexion->prepare($sqlEliminarMiembros);
            $stmtEliminarMiembros->bindParam(1, $idGrupo, PDO::PARAM_INT);
            $stmtEliminarMiembros->execute();

            // Luego eliminar el grupo
            $sqlEliminarGrupo = "DELETE FROM Grupos WHERE id_grupo = ?";
            $stmtEliminarGrupo = $conexion->prepare($sqlEliminarGrupo);
            $stmtEliminarGrupo->bindParam(1, $idGrupo, PDO::PARAM_INT);
            
            if ($stmtEliminarGrupo->execute()) {
                $conexion->commit();
                return [
                    'exito' => true,
                    'mensaje' => 'Grupo eliminado exitosamente'
                ];
            } else {
                $conexion->rollback();
                return [
                    'exito' => false,
                    'mensaje' => 'Error al eliminar el grupo'
                ];
            }
        } catch (PDOException $e) {
            $conexion->rollback();
            error_log("Error al eliminar grupo: " . $e->getMessage());
            return [
                'exito' => false,
                'mensaje' => 'Error en la base de datos al eliminar el grupo'
            ];
        }
    }

    public function eliminarMiembro($conexion, $idMiembroGrupo) {
        try {
            // Verificar que el miembro existe
            $sqlVerificar = "SELECT COUNT(*) FROM MiembrosGrupo WHERE id_miembro_grupo = ?";
            $stmtVerificar = $conexion->prepare($sqlVerificar);
            $stmtVerificar->bindParam(1, $idMiembroGrupo, PDO::PARAM_INT);
            $stmtVerificar->execute();
            
            if ($stmtVerificar->fetchColumn() == 0) {
                return [
                    'exito' => false,
                    'mensaje' => 'El miembro no existe en el grupo'
                ];
            }

            // Eliminar el miembro del grupo
            $sql = "DELETE FROM MiembrosGrupo WHERE id_miembro_grupo = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(1, $idMiembroGrupo, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return [
                    'exito' => true,
                    'mensaje' => 'Miembro eliminado del grupo exitosamente'
                ];
            } else {
                return [
                    'exito' => false,
                    'mensaje' => 'Error al eliminar el miembro del grupo'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error al eliminar miembro: " . $e->getMessage());
            return [
                'exito' => false,
                'mensaje' => 'Error en la base de datos al eliminar el miembro'
            ];
        }
    }

    public function actualizarGrupo($conexion, $idGrupo, $nuevoNombre) {
        try {
            // Validar que el nuevo nombre no esté vacío
            if (empty(trim($nuevoNombre))) {
                return [
                    'exito' => false,
                    'mensaje' => 'El nombre del grupo es obligatorio'
                ];
            }

            // Verificar que el grupo existe
            $sqlVerificarExistencia = "SELECT COUNT(*) FROM Grupos WHERE id_grupo = ?";
            $stmtVerificar = $conexion->prepare($sqlVerificarExistencia);
            $stmtVerificar->bindParam(1, $idGrupo, PDO::PARAM_INT);
            $stmtVerificar->execute();
            
            if ($stmtVerificar->fetchColumn() == 0) {
                return [
                    'exito' => false,
                    'mensaje' => 'El grupo no existe'
                ];
            }

            // Verificar si ya existe otro grupo con el mismo nombre para el mismo docente
            $sqlVerificarNombre = "SELECT COUNT(*) FROM Grupos WHERE nombre = ? AND id_grupo != ? AND docente = (SELECT docente FROM Grupos WHERE id_grupo = ?)";
            $stmtVerificarNombre = $conexion->prepare($sqlVerificarNombre);
            $stmtVerificarNombre->bindParam(1, $nuevoNombre, PDO::PARAM_STR);
            $stmtVerificarNombre->bindParam(2, $idGrupo, PDO::PARAM_INT);
            $stmtVerificarNombre->bindParam(3, $idGrupo, PDO::PARAM_INT);
            $stmtVerificarNombre->execute();
            
            if ($stmtVerificarNombre->fetchColumn() > 0) {
                return [
                    'exito' => false,
                    'mensaje' => 'Ya tienes otro grupo con ese nombre'
                ];
            }

            // Actualizar el nombre del grupo
            $sql = "UPDATE Grupos SET nombre = ? WHERE id_grupo = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(1, $nuevoNombre, PDO::PARAM_STR);
            $stmt->bindParam(2, $idGrupo, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return [
                    'exito' => true,
                    'mensaje' => 'Grupo actualizado exitosamente'
                ];
            } else {
                return [
                    'exito' => false,
                    'mensaje' => 'Error al actualizar el grupo'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error al actualizar grupo: " . $e->getMessage());
            return [
                'exito' => false,
                'mensaje' => 'Error en la base de datos al actualizar el grupo'
            ];
        }
    }

    // Métodos para alumnos
    public function unirseGrupo($conexion, $codigoGrupo, $idAlumno) {
        try {
            // Verificar que el código no esté vacío
            if (empty(trim($codigoGrupo))) {
                return [
                    'exito' => false,
                    'mensaje' => 'El código de acceso es obligatorio'
                ];
            }

            // Verificar que el grupo existe
            $sqlVerificarGrupo = "SELECT id_grupo FROM Grupos WHERE clave = ?";
            $stmtVerificarGrupo = $conexion->prepare($sqlVerificarGrupo);
            $stmtVerificarGrupo->bindParam(1, $codigoGrupo, PDO::PARAM_STR);
            $stmtVerificarGrupo->execute();
            
            $grupo = $stmtVerificarGrupo->fetch(PDO::FETCH_ASSOC);
            if (!$grupo) {
                return [
                    'exito' => false,
                    'mensaje' => 'El código de acceso no es válido'
                ];
            }

            // Verificar que el alumno no esté ya en un grupo
            $sqlVerificarMiembro = "SELECT COUNT(*) FROM MiembrosGrupo WHERE id_usuario = ?";
            $stmtVerificarMiembro = $conexion->prepare($sqlVerificarMiembro);
            $stmtVerificarMiembro->bindParam(1, $idAlumno, PDO::PARAM_INT);
            $stmtVerificarMiembro->execute();
            
            if ($stmtVerificarMiembro->fetchColumn() > 0) {
                return [
                    'exito' => false,
                    'mensaje' => 'Ya perteneces a un grupo. Debes salir del grupo actual primero.'
                ];
            }

            // Agregar al alumno al grupo
            $sql = "INSERT INTO MiembrosGrupo (id_grupo, id_usuario) VALUES (?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(1, $grupo['id_grupo'], PDO::PARAM_INT);
            $stmt->bindParam(2, $idAlumno, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return [
                    'exito' => true,
                    'mensaje' => '¡Te has unido al grupo exitosamente!'
                ];
            } else {
                return [
                    'exito' => false,
                    'mensaje' => 'Error al unirse al grupo'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error al unirse al grupo: " . $e->getMessage());
            return [
                'exito' => false,
                'mensaje' => 'Error en la base de datos al unirse al grupo'
            ];
        }
    }

    public function verificarGrupoAlumno($conexion, $idAlumno) {
        try {
            $sql = "SELECT COUNT(*) FROM MiembrosGrupo WHERE id_usuario = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(1, $idAlumno, PDO::PARAM_INT);
            $stmt->execute();
            
            $tieneGrupo = $stmt->fetchColumn() > 0;
            
            return [
                'exito' => true,
                'tieneGrupo' => $tieneGrupo
            ];
        } catch (PDOException $e) {
            error_log("Error al verificar grupo del alumno: " . $e->getMessage());
            return [
                'exito' => false,
                'mensaje' => 'Error al verificar el estado del grupo'
            ];
        }
    }

    public function obtenerGrupoDeAlumno($conexion, $idAlumno) {
        try {
            // Obtener el grupo del alumno con información completa
            $sql = "
                SELECT 
                    g.id_grupo,
                    g.nombre,
                    g.clave,
                    u_docente.nombre as nombre_docente,
                    u_docente.apellidos as apellidos_docente,
                    u_docente.correo as correo_docente
                FROM MiembrosGrupo mg
                INNER JOIN Grupos g ON mg.id_grupo = g.id_grupo
                INNER JOIN Usuarios u_docente ON g.docente = u_docente.id_usuario
                WHERE mg.id_usuario = ?
            ";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(1, $idAlumno, PDO::PARAM_INT);
            $stmt->execute();
            
            $grupoInfo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$grupoInfo) {
                return [
                    'exito' => false,
                    'mensaje' => 'No perteneces a ningún grupo'
                ];
            }

            // Obtener todos los miembros del grupo
            $sqlMiembros = "
                SELECT 
                    u.nombre,
                    u.apellidos,
                    u.correo,
                    mg.id_miembro_grupo
                FROM MiembrosGrupo mg
                INNER JOIN Usuarios u ON mg.id_usuario = u.id_usuario
                WHERE mg.id_grupo = ?
            ";
            $stmtMiembros = $conexion->prepare($sqlMiembros);
            $stmtMiembros->bindParam(1, $grupoInfo['id_grupo'], PDO::PARAM_INT);
            $stmtMiembros->execute();
            $miembros = $stmtMiembros->fetchAll(PDO::FETCH_ASSOC);

            return [
                'exito' => true,
                'grupo' => [
                    'id_grupo' => $grupoInfo['id_grupo'],
                    'nombre' => $grupoInfo['nombre'],
                    'clave' => $grupoInfo['clave'],
                    'docente' => [
                        'nombre' => $grupoInfo['nombre_docente'],
                        'apellidos' => $grupoInfo['apellidos_docente'],
                        'correo' => $grupoInfo['correo_docente']
                    ],
                    'miembros' => $miembros,
                    'total_miembros' => count($miembros)
                ]
            ];
        } catch (PDOException $e) {
            error_log("Error al obtener grupo del alumno: " . $e->getMessage());
            return [
                'exito' => false,
                'mensaje' => 'Error al obtener la información del grupo'
            ];
        }
    }

    public function salirDeGrupo($conexion, $idAlumno) {
        try {
            // Verificar que el alumno esté en un grupo
            $sqlVerificar = "SELECT COUNT(*) FROM MiembrosGrupo WHERE id_usuario = ?";
            $stmtVerificar = $conexion->prepare($sqlVerificar);
            $stmtVerificar->bindParam(1, $idAlumno, PDO::PARAM_INT);
            $stmtVerificar->execute();
            
            if ($stmtVerificar->fetchColumn() == 0) {
                return [
                    'exito' => false,
                    'mensaje' => 'No perteneces a ningún grupo'
                ];
            }

            // Eliminar al alumno del grupo
            $sql = "DELETE FROM MiembrosGrupo WHERE id_usuario = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(1, $idAlumno, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return [
                    'exito' => true,
                    'mensaje' => 'Has salido del grupo exitosamente'
                ];
            } else {
                return [
                    'exito' => false,
                    'mensaje' => 'Error al salir del grupo'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error al salir del grupo: " . $e->getMessage());
            return [
                'exito' => false,
                'mensaje' => 'Error en la base de datos al salir del grupo'
            ];
        }
    }
}
?>