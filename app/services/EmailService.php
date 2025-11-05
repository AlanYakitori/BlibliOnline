<?php 
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require 'PHPMailer/Exception.php';
    require 'PHPMailer/PHPMailer.php';
    require 'PHPMailer/SMTP.php';
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
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'biblionlinee@gmail.com';                     //SMTP username
            $mail->Password   = 'rjgt wnnz ghou llyy';                               //SMTP password
            $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('biblionlinee@gmail.com', 'Biblionline');
            $mail->addAddress($email, 'Correo Prueba');     //Add a recipient

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Recuperacion de contraseña';

            switch($tipoUsuario){
                case 'administrador':
                    $mail->Body= 'Hola Administrador, este es un correo generado para recuperar tu contrasenia, por favor,
                    visite el siguiente enlace <a href="localhost/BlibliOnline/app/views/auth/cambiarContrasenia.php?id='.$email.'">Clic aqui!</a>, Si usted 
                    no solicito esta accion, haga caso omiso de este mensaje ';
                    break;

                case 'docente':
                    $mail->Body= 'Hola Docente, este es un correo generado para recuperar tu contrasenia, por favor,
                    visite el siguiente enlace <a href="localhost/BlibliOnline/app/views/auth/cambiarContrasenia.php?id='.$email.'">Clic aqui!</a>, Si usted 
                    no solicito esta accion, haga caso omiso de este mensaje ';
                    break;

                case 'alumno':
                    $mail->Body= 'Hola Alumno, este es un correo generado para recuperar tu contrasenia, por favor,
                    visite el siguiente enlace <a href="localhost/BlibliOnline/app/views/auth/cambiarContrasenia.php?id='.$email.'">Clic aqui!</a>, Si usted 
                    no solicito esta accion, haga caso omiso de este mensaje ';
                    break;
                default:
                    $mail->Body    = 'Tipo de usuario no reconocido.';
                    break;
            }

            $mail->send();
            header("Location: ../../index.php?message=ok");

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            header("Location: index.php?message=error");

        }

    }else{
        header("Location: index.php?message=error");
    }

?>