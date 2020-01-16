<?php
    require_once('../PHPMailer/vendor/autoload.php');
    require_once('./config.php');

	use PHPMailer\PHPMailer\PHPMailer;

	$mail = new PHPMailer();

	//Configuración
	$mail->isSMTP();
	$mail->SMTPAuth = true;
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->Username = SMTP_SENDER_EMAIL;
    $mail->Password = SMTP_SENDER_EMAIL_PASSWORD;
    $mail->CharSet = 'UTF-8';
	$mail->isHTML(true);  // Set email format to HTML

    $clientEmail = $_POST['email'];
    $clientName = $_POST['cliente'];

	$mail->SetFrom('info@chispuditoexpress.com', 'Chispudito Express: No responder');
    $mail->AddAddress($clientEmail, $clientName);

	$mail->Subject = '¡Ya tenemos tus paquetes!';
	$mail->Body = $_POST['mensaje'];

	if(!$mail->send()){
	    echo 'Error: ' . $mail->ErrorInfo;
	} else {

	    if (SMTP_SEND_EXTRA_EMAIL) {

            $extraMail = new PHPMailer();
            $extraMail->isSMTP();
            $extraMail->SMTPAuth = true;
            $extraMail->Host = 'smtp.gmail.com';
            $extraMail->SMTPSecure = 'tls';
            $extraMail->Port = 587;
            $extraMail->Username = SMTP_SENDER_EMAIL;
            $extraMail->Password = SMTP_SENDER_EMAIL_PASSWORD;
            $extraMail->CharSet = 'UTF-8';
            $extraMail->isHTML(true);  // Set email format to HTML

            $extraMail->SetFrom('info@chispuditoexpress.com', 'Chispudito Express: No responder');
            $extraMail->AddAddress(SMTP_EXTRA_EMAIL_RECEIVER, 'Chispudito Express');
            $extraMail->Subject = 'Notificación por email enviada a ' . $_POST['cliente'];

            $prependedBody = "
                <br>
                <p style='padding: 0 20px'>
                    Se le envió el siguiente email al cliente <b>$clientName</b>, a su correo <b>$clientEmail</b>.
                    <br>
                    El contenido de la notificación es el siguiente:
                </p>
                <br><br>";
            $extraMail->Body = $prependedBody . $_POST['mensaje'] ;
            $extraMail->send();
        }
	    echo 'Enviado';
	}
?>