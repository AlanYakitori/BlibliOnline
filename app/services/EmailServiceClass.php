<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once 'PHPMailer/Exception.php';
require_once 'PHPMailer/PHPMailer.php';
require_once 'PHPMailer/SMTP.php';

class EmailService {
    private $mail;
    
    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->configurarSMTP();
    }
    
    private function configurarSMTP() {
        try {
            // Server settings
            $this->mail->isSMTP();
            $this->mail->Host       = 'smtp.gmail.com';
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = 'biblionlinee@gmail.com';
            $this->mail->Password   = 'rjgt wnnz ghou llyy';
            $this->mail->Port       = 587;
            
            // Recipients
            $this->mail->setFrom('biblionlinee@gmail.com', 'BiblioOnline');
            
            // Content
            $this->mail->isHTML(true);
            $this->mail->CharSet = 'UTF-8';
        } catch (Exception $e) {
            error_log("Error configurando SMTP: " . $e->getMessage());
        }
    }
    
    public function enviarEmail($destinatario, $asunto, $mensaje, $nombreDestinatario = '') {
        try {
            // Limpiar destinatarios anteriores
            $this->mail->clearAddresses();
            
            // Agregar destinatario
            $this->mail->addAddress($destinatario, $nombreDestinatario);
            
            // Contenido
            $this->mail->Subject = $asunto;
            $this->mail->Body    = $mensaje;
            
            // Enviar
            $resultado = $this->mail->send();
            
            if ($resultado) {
                return [
                    'exito' => true,
                    'mensaje' => 'Email enviado correctamente'
                ];
            } else {
                return [
                    'exito' => false,
                    'mensaje' => 'No se pudo enviar el email'
                ];
            }
            
        } catch (Exception $e) {
            error_log("Error enviando email: " . $e->getMessage());
            return [
                'exito' => false,
                'mensaje' => 'Error al enviar email: ' . $e->getMessage()
            ];
        }
    }
    
    public function enviarEmailRecuperacion($email, $tipoUsuario) {
        $asunto = 'Recuperacion de contraseña';
        
        switch($tipoUsuario){
            case 'administrador':
                $mensaje = 'Hola Administrador, este es un correo generado para recuperar tu contrasenia, por favor,
                visite el siguiente enlace <a href="localhost/BlibliOnline/app/views/auth/cambiarContrasenia.php?id='.$email.'">Clic aqui!</a>, Si usted 
                no solicito esta accion, haga caso omiso de este mensaje ';
                break;

            case 'docente':
                $mensaje = 'Hola Docente, este es un correo generado para recuperar tu contrasenia, por favor,
                visite el siguiente enlace <a href="localhost/BlibliOnline/app/views/auth/cambiarContrasenia.php?id='.$email.'">Clic aqui!</a>, Si usted 
                no solicito esta accion, haga caso omiso de este mensaje ';
                break;

            case 'alumno':
                $mensaje = 'Hola Alumno, este es un correo generado para recuperar tu contrasenia, por favor,
                visite el siguiente enlace <a href="localhost/BlibliOnline/app/views/auth/cambiarContrasenia.php?id='.$email.'">Clic aqui!</a>, Si usted 
                no solicito esta accion, haga caso omiso de este mensaje ';
                break;
                
            default:
                $mensaje = 'Tipo de usuario no reconocido.';
                break;
        }
        
        return $this->enviarEmail($email, $asunto, $mensaje);
    }
}
?>