<?php 
    require_once __DIR__ . '/EmailServiceClass.php';
    require_once __DIR__ . '/../../config/conexion.php';
    require_once __DIR__ . '/../models/UserModel.php';

    if(!isset($_POST['correoElectronico'])) {
        header("Location: index.php?message=error");
        exit();
    }

    $email = $_POST['correoElectronico'];
    $usuario = new UserModel();
    $usuario->setCorreo($email);
    $resultado = $usuario->obtenerTipoUsuarioPorCorreo($conexion);
    $tipoUsuario = $resultado['tipoUsuario'];

    if($tipoUsuario){
        $emailService = new EmailService();
        
        try {
            $resultadoEmail = $emailService->enviarEmailRecuperacion($email, $tipoUsuario);
            
            if($resultadoEmail['exito']) {
                header("Location: ../../index.php?message=ok");
            } else {
                error_log("Error enviando email de recuperación: " . $resultadoEmail['mensaje']);
                header("Location: index.php?message=error");
            }

        } catch (Exception $e) {
            error_log("Excepción al enviar email: " . $e->getMessage());
            header("Location: index.php?message=error");
        }

    } else {
        header("Location: index.php?message=error");
    }

?>