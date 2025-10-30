<?php 

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require 'PHPMailer/Exception.php';
    require 'PHPMailer/PHPMailer.php';
    require 'PHPMailer/SMTP.php';
 
    $servidor = "localhost";
    $usuario = "root";
    $contra = "";
    $db = "biblionline";

    $conexion = new mysqli($servidor, $usuario, $contra, $db);

    $email = $_POST['correoElectronico'];
    $query = "SELECT correo,'administrador' AS tabla FROM administrador WHERE correo='$email' UNION SELECT correo,'alumno' AS tabla FROM alumno WHERE correo='$email' UNION SELECT correo,'docente' AS tabla FROM docente WHERE correo='$email'";

    $result = $conexion->query($query);
    $row = $result->fetch_assoc();

    if($result->num_rows > 0){

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
            $mail->Subject = 'Recuperacion de contrasenia';
            $mail->Body    = 'Hola, este es un correo generado para recuperar tu contrasenia, por favor,
             visite el siguiente enlace <a href="localhost/Estancia_2/BlibliOnline/cambiarContrasenia.php?id='.$row['id_admin'].'">Clic aqui!</a>, Si usted 
             no solicito esta accion, haga caso omiso de este mensaje';

            $mail->send();
            header("Location: index.php?message=ok");

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            header("Location: index.php?message=error");

        }

    }else{
        header("Location: index.php?message=error");
    }

?>


if (url.includes('registroAdministrador')) {
        tipoUsuario = 'administrador';
    } else if (url.includes('registroDocente')) {
        tipoUsuario = 'docente';
    } else if (url.includes('registroAlumno')) {
        tipoUsuario = 'alumno';
    }