<?php
header('Content-Type: application/json;charset=utf-8');
require_once("../db/utils.php");
require_once("../db/db_vars.php");
require_once("../db/server_db_vars.php");
require_once("./notification_template.php");
require_once('../classes/CosteadorPaquetes.php');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$notificationType = $_POST['notificationType'];
$uid = $_POST['uid'];
$uname = $_POST['uname'];
$trackings = $_POST['trackings'];

$query = " 
    SELECT p.servicio, p.tracking, guide_number, libras, cobro_extra, ca.fecha as fecha_ingreso,
    t.precio_fob, t.arancel, t.poliza, t.tarifa_especial as tarifa_express_especial, c.tarifa as tarifa_estandar,
    c.tarifa_express, c.desaduanaje_express as desaduanaje, c.seguro
    FROM paquete p LEFT JOIN tarifacion_paquete_express t ON p.tracking = t.tracking
    LEFT JOIN cliente c on p.uid = c.cid COLLATE utf8_unicode_ci
    LEFT JOIN carga ca on ca.rcid = p.rcid
    WHERE p.uid = '$uid' AND p.tracking IN ('" . implode('\',\'', $trackings) . "')";

$infoPaquetes = [];
$result = $conn->query($query);
if (isset($result) && $result !== false) {
    while($row = mysqli_fetch_assoc($result)) {
        $infoPaquetes[] = $row;
    }
}

$costeador = new CosteadorPaquetes($infoPaquetes);
$costeador->setIsNotificacion(true);

$tableData = $costeador->costear();
$invalidPaquetes = $tableData['invalid_paquetes'];

if (!empty($invalidPaquetes)) {
    echo json_encode([
        'success' => false,
        'message' => 'No se puede enviar la notificación debido uno o más de los paquetes ' .
            'seleccionados no poseen la información necesaria para calcular sus costos.'
    ]);
    exit;
}

if ($notificationType === 'email') {
    $notification = getEmailNotification($uname, $uid, $tableData);
}
else {
    $notification = getWhatsAppNotification($uname, $uid, $tableData);
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