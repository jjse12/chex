<?php
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	require_once("vendor/autoload.php");


	$mail = new PHPMailer();
	
	//Configuración
	$mail->isSMTP();
	$mail->SMTPAuth = true;
	/*
	$mail->Host = "smtpout.secureserver.net";
	$mail->Username = 'info@chispuditoexpress.com';
	$mail->Password = 'Sanchez14587';
	$mail->Port = 25;
	*/

    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPSecure = 'tls';
    $mail->Username = 'chispuditoexpressgt@gmail.com';

    $mail->Password = 'Chex2018';
    $mail->Port = 587;


	$mail->SetFrom('info@chispuditoexpress.com', 'Chispudito Express: No responder');
	//$mail->AddReplyTo('info@chispuditoexpress.com', 'No Reply');

	$mail->AddAddress($_POST["email"], $_POST["cliente"]);
	
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
	    echo 'Enviado';
	}
?>