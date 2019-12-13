<?php

header('Content-Type: application/json;charset=utf-8');
require_once('factura_db_vars.php');
$conn = new mysqli(FACTURA_DB_HOST, FACTURA_DB_USER, FACTURA_DB_PASS, FACTURA_DB_NAME);
$query = "
    SELECT *
    FROM servicio
    ";

$result = $conn->query($query);
if (isset($result) && $result !== false) {
    $data = [];
    while($row = mysqli_fetch_assoc($result)){
        $row['nombre'] = utf8_encode($row['nombre']);
        $row['descripcion'] = utf8_encode($row['descripcion']);
        $row['aviso'] = utf8_encode($row['aviso']);
        $data[] = $row;
    }
    echo json_encode(['data' => $data]);
}
else {
    header("HTTP/1.1 500 Internal Server Error");
}
$conn->close();