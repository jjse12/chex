<?php
    $db_connection = new mysqli("198.71.225.64", "chispuditoex", "Chispudito2015", "usercreator");
    $email = $db_connection->real_escape_string(strip_tags($_POST['email'], ENT_QUOTES));
    $sql = "SELECT * FROM cliente WHERE email = '" . $email . "';";
    $query_check_user_name = $db_connection->query($sql);
    if ($query_check_user_name->num_rows == 1) {
        $row = mysqli_fetch_row($query_check_user_name);
        $htmlbody = "<html><head><style>p {text-align: justify;}</style></head><body><p>Hola " . $row[2] . " " . $row[3] . ",<br/><br/>Para el equipo de Chispudito Express es un gusto poder servirte. Debido a una solicitud de recuperación de usuario realizada en nuestra pagina web, te enviamos aqui la información de tu usuario para que puedas seguir utilizando nuestro servicio.<br/><br/>Nombre Shipping: CHEX " . $row[1] . " <br/>Dirección de envío: 8590 NW 72Th St, Suite CHEX<br/>Ciudad: Miami<br/>Estado: Florida<br/>País: Estados Unidos<br/>Teléfono: (305) 468-8051<br/>Código Postal (Zip Code): 33166 o 33166-2300<br/><br/>Si tienes alguna duda o comentario puedes llamar a nuestras oficinas o escribirnos un correo.<br/><br/>Te recordamos que al momento de realizar tu pedido favor informarnos del número de rastreo para poder darte el servicio de monitoreo de tu compra diariamente. Puedes enviarnos el número de rastreo a través de nuestro formulario <a href='http://www.chispuditoexpress.com/tracking.html'>aqui</a>, ingresando a la pagina: http://www.chispuditoexpress.com/tracking.html o nos puedes enviar un correo con la informacion a info@chispuditoexpress.com<br/><br/>Tambien te pedimos revisar los términos y condiciones de Chispudito Express puedes ingresando <a href='http://www.chispuditoexpress.com/terminos.html'>aqui</a> o puedes ingresar con el enlace siguiente: http://www.chispuditoexpress.com/terminos.html<br/></p>
    <p>Saludos Cordiales,<br /></p>
    <img height='150px' src='http://www.chispuditoexpress.com/images/logocorreo.png'>
    <p><a href='http://www.chispuditoexpress.com'>www.chispuditoexpress.com</a><br />Tels: (502) 2308-6120, 5757-5101<br />Facebook: <a href='https://www.facebook.com/pages/Chispudito-Express/678825928877348'>Chispudito Express</a></p>
    </body>
    </html>";
        //TEXT BODY
        $textbody = "Hola " . $row[2] . " " . $row[3] . ",\r\n\r\n" . "Para el equipo de Chispudito Express es un gusto poder servirte. Debido a una solicitud de recuperación de usuario realizada en nuestra pagina web, te enviamos aqui la información de tu usuario para que puedas seguir utilizando nuestro servicio. \r\n\r\nNombre Shipping: CHEX " . $row[1] . "\r\nDirección de envío: 8590 NW 72Th St, Suite CHEX\r\nCiudad: Miami\r\nEstado: Florida\r\nPaís: Estados Unidos\r\nTeléfono: (305) 468-8051\r\nCódigo Postal (Zip Code): 33166 o 33166-2300\r\n\r\nSi tienes alguna duda o comentario puedes llamar a nuestras oficinas o escribirnos un correo.\r\n\r\nTe recordamos que al momento de realizar tu pedido favor informarnos del número de rastreo para poder darte el servicio de monitoreo de tu compra diariamente. Puedes enviarnos el número de rastreo a través de nuestro formulario  en la pagina de internet: http://www.chispuditoexpress.com/tracking.html o nos puedes enviar un correo con la informacion a info@chispuditoexpress.com\r\n Tambien te pedimos revisar los términos y condiciones de Chispudito Express puedes ingresando a http://www.chispuditoexpress.com/terminos.html \r\nSi tiene consultas, por favor envie un correo a: info@chispuditoexpress.com\r\n\r\nSaludos Cordiales,\r\nChispudito Express\r\n\r\n
    www.chispuditoexpress.com\r\n
    Tels: (502) 2308-6120, 5757-5101\r\n
    Facebook: Chispudito Express (https://www.facebook.com/pages/Chispudito-Express/678825928877348)";

        require "PHPMailerAutoload.php";
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'relay-hosting.secureserver.net';
        $mail->Port = 25;
        $mail->SMTPDebug = 0;

        $mail->SetFrom('info@chispuditoexpress.com', 'Chispudito Express');
        $mail->AddReplyTo('info@chispuditoexpress.com', 'Chispudito Express');
        $mail->AddAddress($email, $nombre);
        $mail->AddBCC('usuarioschex@gmail.com', 'Recuperacion Usuarios CHEX');
        $mail->Subject = 'Recuperacion de Usuario Chispudito Express';
        $mail->AltBody = $textbody;
        $mail->MsgHTML($htmlbody);
        $mail->CharSet = 'UTF-8';

        if(!$mail->send()) {
            echo 'errormail';
        } else {
            echo 'success';
        }
    }
    else
    {
        echo 'nonexist';
    }
?>
