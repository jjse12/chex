<?php

header('Content-Type: application/json;charset=utf-8');

if( empty($_GET['factura_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Error en la solicitud enviada.'
    ]);
    exit;
}

require_once('factura_db_vars.php');
$conn = new mysqli(FACTURA_DB_HOST, FACTURA_DB_USER, FACTURA_DB_PASS, FACTURA_DB_NAME);
$query = "
    SELECT f.id, uid, uname, tracking, description, amount, item_count, guide_number, fob_price, pendiente, date_created, 
        s.nombre AS service
    FROM factura f LEFT JOIN servicio s ON f.service_id = s.id
    WHERE f.id = {$_GET['factura_id']}";

$result = $conn->query($query);
if (isset($result) && $result !== false) {
    $data = mysqli_fetch_assoc($result);
    $data['service'] = utf8_encode($data['service']);
    echo json_encode([
        'success'   => true,
        'data'      => $data
    ]);
}
else {
    header("HTTP/1.1 500 Internal Server Error");
}

$conn->close();
exit;