<?php

    header('Content-Type: application/json;charset=utf-8');
    require_once("server_db_vars.php");
    $conn = new mysqli(SERVER_DB_HOST, SERVER_DB_USER, SERVER_DB_PASS, SERVER_DB_NAME);
    $conn->set_charset('utf8mb4');
    $query = "UPDATE factura SET " . $_POST["set"]." WHERE " . $_POST["where"];
    $result = $conn->query($query);
    if ($result){
        echo json_encode([
            'success'   => true,
            'data'      => [
                'updatedRows' => $conn->affected_rows
            ]
        ]);
    }
    else{
        echo json_encode([
            'success'   => false,
            'data'      => null,
        ]);
    }
    $conn->close();