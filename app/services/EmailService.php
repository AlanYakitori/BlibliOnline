<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/PHPMailer/Exception.php';
require_once __DIR__ . '/../vendor/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../vendor/PHPMailer/SMTP.php';

class EmailService {
    private $mail;
    
    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->configureSMTP();
    }
    
    private function configureSMTP() {
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'biblionlinee@gmail.com';
        $this->mail->Password = 'rjgt wnnz ghou llyy';
        $this->mail->Port = 587;
        $this->mail->setFrom('biblionlinee@gmail.com', 'Biblionline');
    }
    
    // FUNCIONALIDAD DE recovery.php
    public function sendPasswordResetEmail($email, $userType) {
        try {
            $this->mail->addAddress($email, 'Usuario Biblionline');
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Recuperación de contraseña';
            
            $resetLink = $this->generateResetLink($email, $userType);
            $this->mail->Body = $this->getEmailTemplate($userType, $resetLink);
            
            return $this->mail->send();
            
        } catch (Exception $e) {
            error_log("Error enviando email: {$this->mail->ErrorInfo}");
            return false;
        }
    }
    
    private function generateResetLink($email, $userType) {
        $baseUrl = "http://localhost/BlibliOnline/app/views/auth/";
        return $baseUrl . "cambiar_contrasenia_{$userType}.php?email=" . urlencode($email);
    }
    
    private function getEmailTemplate($userType, $resetLink) {
        $userTypeText = ucfirst($userType);
        return "
            <h2>Recuperación de Contraseña</h2>
            <p>Hola {$userTypeText},</p>
            <p>Este es un correo generado para recuperar tu contraseña.</p>
            <p>Por favor, visita el siguiente enlace:</p>
            <p><a href='{$resetLink}'>Cambiar Contraseña</a></p>
            <p>Si usted no solicitó esta acción, haga caso omiso de este mensaje.</p>
        ";
    }
}
?>