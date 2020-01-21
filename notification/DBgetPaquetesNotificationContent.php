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

$costeador = new CosteadorPaquetes($infoPaquetes);

$query = "SELECT tarifa, tarifa_express FROM cliente WHERE cid = '$uid'";
$result = $conn->query($query);
if (!empty($result) && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $costeador->setTarifaEstandar(floatval($row['tarifa']));
    $costeador->setTarifaExpress(floatval($row['tarifa_express']));
}

$tableData = $costeador->costear();
$invalidPaquetes = $tableData['invalid_paquetes'];

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