<?php
header('Content-Type: application/json;charset=utf-8');
require_once("../../db/server_db_vars.php");

$query = "SELECT tarifa, desaduanaje, iva, seguro, cambio_dolar
            FROM cotizador_express_coeficientes WHERE fecha_desactivacion IS NULL";

$serverConn = new mysqli(SERVER_DB_HOST, SERVER_DB_USER, SERVER_DB_PASS, SERVER_DB_NAME);
$res = $serverConn->query($query);
if (!empty($res) && $res->num_rows > 0) {
    $result = $res->fetch_assoc();
    echo json_encode([
        'success' => true,
        'data' => $result
    ]);
    return;
}

header("HTTP/1.1 500 Internal Server Error");
