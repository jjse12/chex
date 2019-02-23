<?php

    header('Content-Type: application/json;charset=utf-8');
    require_once("server_db_vars.php");
    $conn = new mysqli(SERVER_DB_HOST, SERVER_DB_USER, SERVER_DB_PASS, SERVER_DB_NAME);
    $conn->set_charset('utf8mb4');
    $query = "SELECT * FROM factura WHERE visible = 1";
    $result = $conn->query($query);
    if (isset($result) && $result !== false) {
        $data = array();
        while($row = mysqli_fetch_assoc($result)){
            $data[] = $row;
        }
        echo json_encode(['data' => $data]);;
    }
    else {
        header("HTTP/1.1 500 Internal Server Error");
    }