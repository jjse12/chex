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
    $mail->Username = 'jjse127@gmail.com';
    $pass = preg_replace('/[^iJr2cave4A01Vo]+/', "", "klJnMaPvpiIeUuuRr52Qc");
    $mail->Password = $pass;
    $mail->Port = 587;


	$mail->SetFrom('info@chispuditoexpress.com', 'Chispudito Express');
	$mail->AddReplyTo('info@chispuditoexpress.com', 'Chispudito Express');

	$mail->AddAddress($_POST["email"], $_POST["cliente"]);
	
	$mail->isHTML(true);  // Set email format to HTML
	$mail->CharSet = 'UTF-8';
	$mail->Subject = 'Ya tenemos tu pedido!';
	$mail->Body = $_POST['mensaje'];
	
	if(!$mail->send()){
	    echo 'Error: ' . $mail->ErrorInfo;
	} else {
	    echo 'Enviado';
	}
?>