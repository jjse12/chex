<?php
header('Content-Type: application/json;charset=utf-8');
require_once("db/utils.php");
require_once("db/db_vars.php");
require_once("db/server_db_vars.php");
require_once("notification_template.php");

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$notificationType = $_POST['notificationType'];
$uid = $_POST['uid'];
$uname = $_POST['uname'];
$trackings = $_POST['trackings'];

$query = " 
    SELECT p.servicio, p.tracking, p.guide_number, libras, celulares, cobro_extra , t.precio_fob, t.arancel, t.tarifa_especial
    FROM paquete p LEFT JOIN tarifacion_paquete_express t ON p.tracking = t.tracking
    WHERE p.uid = '$uid' AND p.tracking IN ('" . implode('\',\'', $trackings) . "')";

$infoPaquetes = [];
$result = $conn->query($query);
if (isset($result) && $result !== false) {
    while($row = mysqli_fetch_assoc($result)) {
        $formattedRow = [];
        foreach ($row as $key => $value) {
            $formattedRow[$key] = utf8_encode($value);
        }
        $infoPaquetes[] = $formattedRow;
    }
}

$tarifaEstandarCliente = 60;

$query = "SELECT tarifa, tarifa_express FROM cliente WHERE cid = '$uid'";
$result = $conn->query($query);
if (!empty($result) && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $tarifaEstandarCliente = floatval($row['tarifa']);
    $tarifaExpressCliente = floatval($row['tarifa_express']);
}

$cantPaquetes = count($infoPaquetes);
$totalLibras = 0;
$total = 0;
$coeficientesFetched = false;

foreach ($infoPaquetes as &$infoPaquete) {
    $celulares = intval($infoPaquete['celulares']);
    $cobroExtra = floatval($infoPaquete['cobro_extra']);
    $infoPaquete['cobro_celulares'] = $celulares * 100;

    $totalLibras += $infoPaquete['libras'];
    $totalPaquete = $infoPaquete['cobro_celulares'] + $cobroExtra;

    $infoPaquete['invalid'] = false;

    if ($infoPaquete['servicio'] === 'Express') {
        if (empty($infoPaquete['precio_fob']) || empty($infoPaquete['arancel'])){
            echo json_encode([
                'success' => false,
                'message' => "El paquete Express con tracking <b>{$infoPaquete['tracking']}</b> y número de guía <b>" .
                    $infoPaquete['guide_number'] . '</b> aún no ha sido tarifado!'
            ]);
            exit;
        }
        else {
            if (!$coeficientesFetched){
                $serverConn = new mysqli(SERVER_DB_HOST, SERVER_DB_USER, SERVER_DB_PASS, SERVER_DB_NAME);
                $query = "SELECT tarifa, desaduanaje, iva, seguro, cambio_dolar FROM cotizador_express_coeficientes WHERE fecha_desactivacion IS NULL";
                $res = $serverConn->query($query);
                if (!empty($res) && $res->num_rows > 0) {
                    $row = $res->fetch_assoc();
                    $tarifaFetched = floatval($row['tarifa']);
                    $desaduanaje = floatval($row['desaduanaje']);
                    $iva = floatval($row['iva']);
                    $seguro = floatval($row['seguro']);
                    $cambioDolar = floatval($row['cambio_dolar']);

                    $coeficientesFetched = true;
                }
                else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Ocurrió un problema al intentar obtener los coeficientes para el cálculo de los costos de los paquetes express!'
                    ]);
                    exit;
                }
            }

            $tarifa = !empty($infoPaquete['tarifa_especial']) ? $infoPaquete['tarifa_especial'] :
                (!empty($tarifaExpressCliente) ? $tarifaExpressCliente : $tarifaFetched);
            $cotizacion = getCotizacionExpress($tarifa, $infoPaquete['libras'], $infoPaquete['precio_fob'],
                $infoPaquete['arancel'], $desaduanaje, $iva, $seguro, $cambioDolar);

            $totalPaquete += $cotizacion['total'];
            $infoPaquete['total'] = $totalPaquete;
        }
    }
    else if ($infoPaquete['servicio'] === 'Estándar' || $infoPaquete['servicio'] === 'EstÃ¡ndar') {
        $totalPaquete += $tarifaEstandarCliente * $infoPaquete['libras'];
        $infoPaquete['total'] = $totalPaquete;
    }

    $total += $totalPaquete;
}
unset($infoPaquete);

if ($notificationType === 'email') {
    $notification = getEmailNotification($uname, $uid, $infoPaquetes, $totalLibras, $total);
}
else {
    $notification = getWhatsAppNotification($uname, $uid, $infoPaquetes, $totalLibras, $total);
    $notification = str_replace("<ENTER>", "%0A", $notification);
    $notification = str_replace("Ã¡", "á", $notification);
    $notification = str_replace("Ã©", "é", $notification);
    $notification = str_replace("Ã", "í", $notification);
    $notification = str_replace("Ã³", "ó", $notification);
    $notification = str_replace("Ãº", "ú", $notification);
    $notification = str_replace("Ã¼", "ü", $notification);
    $notification = str_replace("Ã±", "ñ", $notification);
    $notification = str_replace(" ", "%20", $notification);
}

echo json_encode([
    'success' => true,
    'data' => $notification
]);