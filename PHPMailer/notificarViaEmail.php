<?php
	use PHPMailer\PHPMailer\PHPMailer;
	require_once('vendor/autoload.php');
    require_once('./config.php');

	$mail = new PHPMailer();

	//Configuración
	$mail->isSMTP();
	$mail->SMTPAuth = true;
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->Username = SMTP_SENDER_EMAIL;
    $mail->Password = SMTP_SENDER_EMAIL_PASSWORD;

    $clientEmail = $_POST['email'];
    $clientName = $_POST['cliente'];

	$mail->SetFrom('info@chispuditoexpress.com', 'Chispudito Express: No responder');
    $mail->AddAddress($clientEmail, $clientName);
	$mail->isHTML(true);  // Set email format to HTML
	$mail->CharSet = 'UTF-8';
	$mail->Subject = '¡Ya tenemos tu pedido!';

	$chexWhats = str_replace(' ', '%20', 'Hola Chispudito Express, quisiera que me entregaran mis paquetes... ');
	$footer = "<br><br>
    <div style='width: 100%;'><a style='width: 50%; margin-left: 25%' href='https://api.whatsapp.com/send?phone=50257575101&text=$chexWhats'>
    <img src='http://www.chispuditoexpress.com/images/whatsapp75px.png'></a><br/><label style='width: 70%;'> Presiona el ícono para responder por whatsapp inmediatamente. </label>
    </div>
    <br>
    <label style='color: indianred;'><i>Por favor no respondas este correo, utiliza nuestros números telefónicos para contactarnos:</i></label>
    <p>Teléfono: (502) 2308-6120<br/>Whatsapp: <a href='https://api.whatsapp.com/send?phone=50257575101&text=$chexWhats' style='color: lightgreen;'>5757-5101</a><br/>
    <a href='http://www.chispuditoexpress.com'>Chispudito Express</a>
    <img height='150px' src='http://www.chispuditoexpress.com/images/logocorreo.png'>
    </p>";
	$mail->Body = $_POST['mensaje'] . $footer;

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

            $extraMail->SetFrom('info@chispuditoexpress.com', 'Chispudito Express: No responder');
            $extraMail->AddAddress(SMTP_EXTRA_EMAIL_RECEIVER, 'Chispudito Express');
            $extraMail->isHTML(true);  // Set email format to HTML
            $extraMail->CharSet = 'UTF-8';
            $extraMail->Subject = 'Notificación por email enviada a ' . $_POST['cliente'];

            $chexWhats = str_replace(' ', '%20', 'Hola Chispudito Express, quisiera que me entregaran mis paquetes... ');
            $footer = "<br><br>
                <div style='width: 100%;'><a style='width: 50%; margin-left: 25%' href='https://api.whatsapp.com/send?phone=50257575101&text=$chexWhats'>
                <img src='http://www.chispuditoexpress.com/images/whatsapp75px.png'></a><br/><label style='width: 70%;'> Presiona el ícono para responder por whatsapp inmediatamente. </label>
                </div>
                <br>
                <label style='color: indianred;'><i>Por favor no respondas este correo, utiliza nuestros números telefónicos para contactarnos:</i></label>
                <p>Teléfono: (502) 2308-6120<br/>Whatsapp: <a href='https://api.whatsapp.com/send?phone=50257575101&text=$chexWhats' style='color: lightgreen;'>5757-5101</a><br/>
                <a href='http://www.chispuditoexpress.com'>Chispudito Express</a>
                <img height='150px' src='http://www.chispuditoexpress.com/images/logocorreo.png'>
                </p>";
            $prependedBody = "<br>
                Se le envio el siguiente email al cliente <b>$clientName</b>, a su correo <b>$clientEmail</b>. El contenido de la notificación es el siguiente:<br><br>";
            $extraMail->Body = $prependedBody . $_POST['mensaje'] . $footer;
            $extraMail->send();
        }
	    echo 'Enviado';
	}
?>