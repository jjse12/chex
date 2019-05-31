<?php

    header('Content-Type: application/json;charset=utf-8');
    require_once('factura_db_vars.php');
    $conn = new mysqli(FACTURA_DB_HOST, FACTURA_DB_USER, FACTURA_DB_PASS, FACTURA_DB_NAME);
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