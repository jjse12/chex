<?php

header('Content-Type: application/json;charset=utf-8');
require_once('servicio_db_vars.php');
require_once('utils.php');

$conn = new mysqli(SERVICIO_DB_HOST, SERVICIO_DB_USER, SERVICIO_DB_PASS, SERVICIO_DB_NAME);
$query = "
    SELECT *
    FROM servicio
    ";

$result = $conn->query($query);
if (isset($result) && $result !== false) {
    echo json_encode(['data' => getServicesFromSqlResult($result)]);
}
else {
    header("HTTP/1.1 500 Internal Server Error");
}
$conn->close();