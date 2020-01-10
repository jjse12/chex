<?php
require_once('config.php');

function getWhatsAppNotification($nombreCliente, $idCliente, $paquetes, $pesoTotal, $costoTotal) : string
{
    // Configuración de palabras para una notificación de múltiples paquetes
    if (count($paquetes) > 1) {
        $enviar = 'envíen';
        $programar = 'programen';
        $message =
            /********* TEXTO DE SALUDO (múltiples paquetes) *********/
            "Hola $nombreCliente / CHEX $idCliente<ENTER>" .
            "Te saludamos de Chispudito Express para informarte que tus siguientes " .
            "paquetes ya están disponibles en nuestras oficinas en Guatemala.<ENTER>Detalle de paquetes:<ENTER><ENTER>";
    }
    // Configuración de palabras para una notificación de un único paquete
    else {
        $enviar = 'envíe';
        $programar = 'programe';
        $message =
            /********* TEXTO DE SALUDO (un único paquete) *********/
            "Hola $nombreCliente / CHEX $idCliente<ENTER>" .
            "Te saludamos de Chispudito Express para informarte que tu siguiente " .
            "paquete ya está disponible en nuestras oficinas en Guatemala.<ENTER>Detalle del paquete:<ENTER><ENTER>";

    }

    foreach ($paquetes as $num => $paquete) {
        /********* TEXTO DE PAQUETES (ESTE TEXTO SE ESCRIBE POR CADA PAQUETE SELECCIONADO) *********/
        $message .=
            "*  Tracking: " . $paquete['tracking'] . '<ENTER> ' .
            "   Peso: " . $paquete['libras'] . " lb". '<ENTER>'.
            "    Costo: Q" . number_format($paquete['total'], 2) . '<ENTER><ENTER>';
    }

    /********* TEXTO TOTALES *********/
    $message .=
        "- Total de paquetes: " . count($paquetes) .  '<ENTER>'  .
//      "- Peso total: ${pesoTotal} lb."  .  '<ENTER>'  .
        "- Total a cancelar: Q" . number_format($costoTotal, 2) . '<ENTER><ENTER>';

    /********* TEXTO DE INFORMACIÓN EXTRA *********/
    // (las variables "$enviar" y "$programar" se asignan arriba
    // dependiendo de si la notifición es para múltiples paquetes o no)
    $message .=
        "El total corresponde a pago en efectivo. Si deseas que se te $enviar en ruta a tu domicilio, " .
        "favor contactar a servicio al cliente para que se te $programar. <ENTER><ENTER>";

    /********* TEXTO DE DESPEDIDA *********/
    $message .=
        'Gracias por tu preferencia, cualquier duda o consulta puedes comunicarte a nuestro servicio al cliente.<ENTER><ENTER>' .
        '*Chispudito Express*.<ENTER>' .
        'Tel: ' . CHEX_CUSTOMER_SERVICE_PHONE_NUMBER . '<ENTER>';

    return $message;
};

function getEmailNotification($nombreCliente, $idCliente, $paquetes, $pesoTotal, $costoTotal): string
{
    $message = "
        <div style='width: auto; padding: 32px 64px; align-self: center; background: #f5f5f5; align-items: center;'>
            <img alt='chispudito-express-logo' style='max-width: 147px; max-height: 100px'
                height='100px' src='http://www.chispuditoexpress.com/images/logocorreo.png'>
            <br><br>";
    // Configuración de palabras para una notificación de múltiples paquetes
    if (count($paquetes) > 1) {
        $enviar = 'envíen';
        $programar = 'programen';
        $message .= "
            <div>
                <span style='color: #ef6400'>Hola $nombreCliente / CHEX $idCliente</span>
                <hr><br>
                <p style='color: #34557A; text-align: justify;'>
                    Te saludamos de Chispudito Express para informarte que tus siguientes
                    paquetes ya están disponibles en nuestras oficinas en Guatemala.
                </p>
                <span style='margin-top: 8px; color: #ef6400'>Detalle de paquetes</span><br><br>
            </div>";
        /********* TEXTO DE SALUDO (múltiples paquetes) *********/
    }
    // Configuración de palabras para una notificación de un único paquete
    else {
        $enviar = 'envíe';
        $programar = 'programe';
        /********* TEXTO DE SALUDO (un único paquete) *********/
        $message .= "
            <div>
                <span style='color: #ef6400'>Hola $nombreCliente / CHEX $idCliente</span>
                <br><br>
                <p style='color: #34557A; text-align: justify;'>
                    Te saludamos de Chispudito Express para informarte que tu siguiente
                    paquete ya está disponible en nuestras oficinas en Guatemala.
                </p>
                <span style='margin-top: 8px; color: #ef6400''>Detalle de paquetes<br><br>
            </div>";
    }

    $rows = '';
    foreach ($paquetes as $num => $paquete) {
        $rows .= "
            <tr>
                <th>{$paquete['tracking']}</th>
                <th>" . $paquete['libras'] . ($paquete['libras'] > 1 ? ' libras' : ' libra') . "</th>
                <th>Q" . number_format($paquete['total'], 2) . "</th>
            </tr>
        ";
    }

    $message .= "
        <table style='width: 100%; text-align: left'>
            <thead>
                <tr>
                    <th># Tracking</th>
                    <th>Peso</th>
                    <th>Costo</th>
                </tr>
            </thead>
            <tbody>
                <tr><th colspan='3'>&nbsp;</th></tr>
                $rows
            </tbody>
            <tfoot>
                <tr><th colspan='3'>&nbsp;</th></tr>
                <tr><th colspan='3'><small>Totales:</small></th></tr>
                <tr>
                    <th>" . count($paquetes) . (count($paquetes) > 1 ? ' paquetes' : ' paquete') . "</th>
                    <th>" . $pesoTotal . ($pesoTotal > 1 ? ' libras' : ' libra') . "</th>
                    <th>Q" . number_format($costoTotal, 2) ."</th>
                </tr>
            </tfoot>
        </table>
    ";

    /********* TEXTO DE INFORMACIÓN EXTRA *********/
    // (las variables "$enviar" y "$programar" se asignan arriba
    // dependiendo de si la notifición es para múltiples paquetes o no)
    $message .= "
        <br>
        <p style='color: #34557A; text-align: justify;'>
            El total corresponde a pago en efectivo. Si deseas que se te $enviar en ruta a tu domicilio,
            favor contactar a servicio al cliente para que se te $programar.
        </p>";

    $chexWhats = str_replace(' ', '%20', 'Hola Chispudito Express, quisiera que me entregaran mis paquetes... ');

    /********* TEXTO DE DESPEDIDA *********/
    $message .= "
        <p style='color: #34557A; text-align: justify;'>
            ¡Gracias por tu preferencia!
            <br><br>
            <span style='margin-top: 10px;'>
            <i style='color: indianred;'>Por favor no respondas este correo</i>,
            cualquier duda o consulta que quieras hacer puedes comunicarte a nuestro servicio al cliente.
            </span>
        </p>
        <br>
        <a href='http://www.chispuditoexpress.com'>Chispudito Express</a>
        <br><br>
        Tel: " . CHEX_CUSTOMER_SERVICE_PHONE_NUMBER . "<br>" .
        "WhatsApp: " . CHEX_WHATSAPP_PHONE_NUMBER .
        "<br>
        <small>
            <a href='https://api.whatsapp.com/send?phone=" . CHEX_WHATSAPP_PHONE_NUMBER_FOR_URL . "&text=$chexWhats'
                style='color: #df8a27;'>(click aqui para comunicarte de inmediato vía WhatsApp)</a>
        </small>
        <br><br>
        <img alt='chispudito-express-logo' style='max-width: 147px; max-height: 100px'
            height='100px' src='http://www.chispuditoexpress.com/images/logocorreo.png'>
        <br>
    </div>";



    $bottomPadding = count($paquetes) > 4 ? '180px' : '100px';
    return "
<html lang='es' xmlns='http://www.w3.org/1999/xhtml'>
    <head>
        <meta charset='utf-8'/>
        <title>America Voice</title>
    </head>
    <body>
        <div style='padding-bottom: $bottomPadding; font-size: 12px; font-family: sans-serif; font-weight: lighter; background:#ffffff;'>
            <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet'>
            <div style='width:100%; margin:auto'>
                <table width='600' align='center' border='0' cellspacing='0' cellpadding='0'>
                    <tbody style=\"font-family: 'Open Sans', sans-serif;\">
                        <tr>
                            <td>
                                <table>
                                    <tbody>
                                    <tr>
                                        <td style='text-align: center;'>
                                            $message
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>";
};