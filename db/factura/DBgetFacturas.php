<?php

header('Content-Type: application/json;charset=utf-8');
require_once('factura_db_vars.php');
$conn = new mysqli(FACTURA_DB_HOST, FACTURA_DB_USER, FACTURA_DB_PASS, FACTURA_DB_NAME);
$query = "
    SELECT f.id, user_chex_code, user_full_name, tracking, description, amount, item_count, guide_number, fob_price, pendiente, date_created, 
        date_delivered, date_received, miami_received, client_notified, s.nombre AS service
    FROM factura f 
    LEFT JOIN factura_logistica fl ON f.id = fl.fid 
    LEFT JOIN servicio s ON f.service_id = s.id
    ORDER BY f.id ASC";

$result = $conn->query($query);
if (isset($result) && $result !== false) {
    $data = array();
    while($row = mysqli_fetch_assoc($result)){
        $row['service'] = utf8_encode($row['service'] ?? "");
        $data[] = $row;
    }
    echo json_encode(['data' => $data]);
}
else {
    header("HTTP/1.1 500 Internal Server Error");
}
$conn->close();