<?php
require_once __DIR__ . '/../../config/conexion.php';

class PreferencesModel {
    private $id;
    private $listaCategorias;
    private $descripcion;

    public function __construct() {
        $this->id = '';
        $this->listaCategorias = [];
        $this->descripcion = '';
    }

    // Getters
    public function getId() { return $this->id; }
    public function getListaCategorias() { return $this->listaCategorias; }
    public function getDescripcion() { return $this->descripcion; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setListaCategorias($listaCategorias) { $this->listaCategorias = $listaCategorias; }
    public function setDescripcion($descripcion) { $this->descripcion = $descripcion; }

    // Método para guardar las preferencias del usuario
    public function guardarPreferencia($conexion) {
        try {
            // Iniciar transacción para asegurar consistencia
            $conexion->beginTransaction();
            
            // Preparar la consulta de inserción
            $sqlInsertar = "INSERT INTO CategoriasUsuario (id_usuario, id_categoria) VALUES (?, ?)";
            $stmtInsertar = $conexion->prepare($sqlInsertar);
            
            // Contador para verificar inserciones exitosas
            $insercionesExitosas = 0;
            
            // Insertar cada categoría seleccionada
            foreach ($this->listaCategorias as $idCategoria) {
                // Validar que el ID de categoría sea un número válido
                if (!is_numeric($idCategoria) || $idCategoria <= 0) {
                    error_log("ID de categoría inválido: " . $idCategoria);
                    continue; // Saltar esta categoría inválida
                }
                
                $stmtInsertar->bindParam(1, $this->id, PDO::PARAM_INT);
                $stmtInsertar->bindParam(2, $idCategoria, PDO::PARAM_INT);
                
                if ($stmtInsertar->execute()) {
                    $insercionesExitosas++;
                } else {
                    error_log("Error al insertar categoría " . $idCategoria . " para usuario " . $this->id);
                }
            }
            
            // Verificar que se insertaron todas las categorías
            if ($insercionesExitosas === count($this->listaCategorias)) {
                // Actualizar el campo 'gusto' del usuario a 1 (ya asignó preferencias)
                $sqlActualizarGusto = "UPDATE usuarios SET gusto = 1 WHERE id_usuario = ?";
                $stmtActualizarGusto = $conexion->prepare($sqlActualizarGusto);
                $stmtActualizarGusto->bindParam(1, $this->id, PDO::PARAM_INT);
                
                if ($stmtActualizarGusto->execute()) {
                    // Confirmar transacción
                    $conexion->commit();
                    return [
                        'exito' => true,
                        'mensaje' => 'Preferencias guardadas exitosamente',
                        'categoriasGuardadas' => $insercionesExitosas
                    ];
                } else {
                    throw new Exception("Error al actualizar el estado del usuario");
                }
            } else {
                throw new Exception("No se pudieron guardar todas las categorías");
            }
            
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $conexion->rollBack();
            error_log("Error en guardarPreferencia: " . $e->getMessage());
            
            return [
                'exito' => false,
                'mensaje' => 'Error al guardar las preferencias: ' . $e->getMessage(),
                'categoriasGuardadas' => 0
            ];
        }
    }
    
}
?>