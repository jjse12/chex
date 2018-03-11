<?php
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	require_once("autoload.php");


	$mail = new PHPMailer();
	
	//Configuración
	$mail->isSMTP();
	$mail->SMTPAuth = true;
	$mail->Host = "smtpout.secureserver.net";
	$mail->Username = 'info@chispuditoexpress.com';
	$mail->Password = 'Sanchez14587';
	$mail->Port = 25; 


	$mail->SetFrom('info@chispuditoexpress.com', 'Chispudito Express');
	//$mail->AddReplyTo('info@chispuditoexpress.com', 'Chispudito Express');
	$mail->AddAddress($_POST["email"], $_POST["cliente"]);
	
	$mail->isHTML(true);  // Set email format to HTML
	$mail->CharSet = 'UTF-8';
	$mail->Subject = 'Paquetes recibidos Chispudito Express';
	$mail->Body = "<br><br>Queremos informarte que los siguientes paquetes han arribado a nuestras bodegas.\n\n".$_POST["paquetes"]."\n Quedamos a la espera para que nos avises de que forma te haremos entrega de tu pedido. Que tengas un buen día.";
	
	if(!$mail->send()){
	    echo 'Error: ' . $mail->ErrorInfo;
	} else {
	    echo 'Enviado';
	}
?>