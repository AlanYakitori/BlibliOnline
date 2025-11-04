<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';

class PreferencesController {
    private $userModel;
    private $categoryModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
        $this->categoryModel = new CategoryModel();
    }
    
    // FUNCIONALIDAD DE procesarPreferencias.php
    public function savePreferences() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datosRecibidos = file_get_contents('php://input');
            $datosPreferencias = json_decode($datosRecibidos, true);
            
            if (!$datosPreferencias) {
                return $this->jsonResponse(false, 'No se recibieron datos');
            }
            
            $idUsuario = $datosPreferencias['idUsuario'];
            $tipoUsuario = $datosPreferencias['tipoUsuario'];
            $categoriasSeleccionadas = $datosPreferencias['categoriasSeleccionadas'];
            
            if (!$idUsuario || !$tipoUsuario || !$categoriasSeleccionadas) {
                return $this->jsonResponse(false, 'Datos incompletos');
            }
            
            try {
                // Actualizar preferencias del usuario
                $resultado = $this->userModel->updateUserPreferences($idUsuario, $tipoUsuario, $categoriasSeleccionadas);
                
                if ($resultado) {
                    // Obtener datos actualizados
                    $datosUsuario = $this->userModel->getUserById($idUsuario, $tipoUsuario);
                    $categoriasUsuario = $this->categoryModel->getUserCategories($idUsuario, $tipoUsuario);
                    
                    unset($datosUsuario['contrasena']);
                    $datosUsuario['tipoUsuario'] = $tipoUsuario;
                    
                    return $this->jsonResponse(true, 'Preferencias guardadas correctamente', $datosUsuario, $categoriasUsuario);
                } else {
                    return $this->jsonResponse(false, 'Error al guardar preferencias');
                }
                
            } catch(Exception $error) {
                return $this->jsonResponse(false, 'Error al guardar preferencias: ' . $error->getMessage());
            }
        }
        return $this->jsonResponse(false, 'Método no permitido');
    }
    
    private function jsonResponse($success, $message, $userData = null, $categories = null) {
        header('Content-Type: application/json');
        $response = ['exito' => $success, 'mensaje' => $message];
        if ($userData) $response['datosUsuario'] = $userData;
        if ($categories) $response['categoriasUsuario'] = $categories;
        echo json_encode($response);
        exit();
    }
}
?>