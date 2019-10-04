<?php

header('Content-Type: application/json;charset=utf-8');
require_once('factura_db_vars.php');
$conn = new mysqli(FACTURA_DB_HOST, FACTURA_DB_USER, FACTURA_DB_PASS, FACTURA_DB_NAME);
$query = "
    SELECT f.id, uid, uname, tracking, description, amount, item_count, pendiente, date_created, 
        date_delivered, date_received, miami_received, client_notified 
    FROM factura f 
    LEFT JOIN factura_logistica fl ON f.id = fl.fid 
    ORDER BY f.id ASC";

$result = $conn->query($query);
if (isset($result) && $result !== false) {
    $data = array();
    while($row = mysqli_fetch_assoc($result)){
        $data[] = $row;
    }
    echo json_encode(['data' => $data]);
}
else {
    header("HTTP/1.1 500 Internal Server Error");
}
$conn->close();