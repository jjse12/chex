<?php
$fullName = $_POST['fullName'];
$userID = $_POST['userID'];
$email = $_POST['email'];
$package_desc = $_POST['package_desc'];
$package_weight = $_POST['package_weight'];
$package_cost = $_POST['package_cost'];
$totalcost = $_POST['totalcost'];
$notes = $_POST['notes'];
$amount_send = $_POST['amount_send'];
$address = $_POST['address'];

//HTML VERSION
$htmlbody =  "<html>
    <head>
        <style>
            table, th, td {
                border: 2px solid black;
                border-collapse: collapse;
            }
            th, td {
                padding: 5px;
                text-align: justify;
            }
            tfoot td {
                font-weight: bold;
            }
            p {
                text-align: justify;
            }
        </style>
    </head>
    <body>
        
        <p>Hola " . $fullName . ", <br /><br />Es un gusto saludarlo deseándole éxitos en sus labores. Por este medio le informamos que hemos recibido los siguientes paquetes en nuestra bodega en ciudad de Guatemala, bajo el usuario " . $userID . ".<br /><br />Abajo encontrará el detalle de los paquetes recibidos: </p>
        
       <table>
          <thead>
            <tr>
              <th>Información del Paquete</th>
              <th>Peso del Paquete</th>
              <th>Costo del Paquete</th>
            </tr>
          </thead>
           <tfoot>
            <tr>
              <td></td>
              <td>Total</td>
              <td>Q" . $totalcost . "</td>
            </tr>
          </tfoot>
          <tbody>";

foreach($package_desc as $key => $n ) {
  $htmlbody .= "<tr>
              <td>" . $n . "</td>
              <td>" . $package_weight[$key] . " lbs.</td>
              <td>Q" . $package_cost[$key] . "</td>
            </tr>";
}

$htmlbody .= "</tbody>
        </table>
        <p>Su dirección principal registrada es:<br /> <br />" . $address . ".<br /><br />Notificarnos si desea que los paquetes se coloquen en ruta a esta dirección. El costo del envío sería de Q" . $amount_send . " adicionales al total.</p>
        <p>" . $notes . "</p>
        
        <p>Saludos Cordiales,<br /></p>
        <img height='150px' src='http://www.chispuditoexpress.com/images/logocorreo.png'>
        <p><a href='http://www.chispuditoexpress.com'>www.chispuditoexpress.com</a><br />Tels: (502) 2308-6120, 5757-5101<br />Facebook: <a href='https://www.facebook.com/pages/Chispudito-Express/678825928877348'>Chispudito Express</a></p>
    </body>
</html>";

//PLAIN TEXT VERSION
$textbody =  "Hola " . $fullName . ",\r\n\r\nEs un gusto saludarlo deseándole éxitos en sus labores. Por este medio le informamos que hemos recibido los siguientes paquetes en nuestra bodega en ciudad de Guatemala, bajo el usuario " . $userID . ".\r\n\r\n";
$textbody .= "Abajo encontrará el detalle de los paquetes recibidos:\r\n\r\n";

foreach($package_desc as $key => $n ) {
    $textbody .= "Paquete #" . $key . ":\r\n";
    $textbody .= "Información del Paquete: " . $n . "\r\n";
    $textbody .= "Peso del Paquete: " . $package_weight[$key] . " lbs\r\n";
    $textbody .= "Costo del Paquete: Q" . $package_cost[$key] . "\r\n\r\n";
}

$textbody .= "Total: Q" . $totalcost . "\r\n\r\nSu dirección principal registrada es:\r\n\r\n" . $address . ".\r\n\r\nNotificarnos si desea que los paquetes se coloquen en ruta a esta dirección. El costo para el envío sería de Q" . $amount_send . " adicionales al total.\r\n\r\n" . $notes . "\r\n\r\n";

$textbody .= "Saludos Cordiales,\r\nChispudito Express\r\n\r\n
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
$mail->AddAddress("jjsanchez@galileo.edu", $fullName);
$mail->AddBCC('notificacioneschex@gmail.com', 'Notificaciones CHEX');
$mail->Subject = 'Paquetes recibidos Chispudito Express';
$mail->AltBody = $textbody;
$mail->MsgHTML($htmlbody);
$mail->CharSet = 'UTF-8';

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}
echo "FINISHED!";
?>