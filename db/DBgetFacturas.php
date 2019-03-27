<?php

header('Content-Type: application/json;charset=utf-8');
require_once("server_db_vars.php");
$conn = new mysqli(SERVER_DB_HOST, SERVER_DB_USER, SERVER_DB_PASS, SERVER_DB_NAME);
$query = "SELECT f.id, uid, uname, tracking, description, amount, pendiente, date_created, date_delivered FROM factura f LEFT JOIN factura_logistica fl ON f.id = fl.fid ORDER BY f.id ASC";
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