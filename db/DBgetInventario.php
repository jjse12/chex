<?php
header('Content-Type: application/json;charset=utf-8');
require_once("db_vars.php");
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$query = "SELECT P.rcid AS rcid, fecha, tracking, uid, uname, libras, plan, cobro_extra, servicio, guide_number FROM paquete P JOIN carga C ON P.rcid = C.rcid WHERE P.estado IS NULL";
$result = $conn->query($query);
if (isset($result) && $result !== false){
    $data = array();
    while($row = mysqli_fetch_assoc($result)){
        $formattedRow = [];
        foreach ($row as $key => $value) {
            $formattedRow[$key] = utf8_encode($value ?? "");
        }
        $data[] = $formattedRow;
    }
    echo json_encode($data);;
    exit;
}

header("HTTP/1.1 500 Internal Server Error");
