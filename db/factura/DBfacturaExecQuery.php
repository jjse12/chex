<?php

header('Content-Type: application/json;charset=utf-8');
require_once('factura_db_vars.php');
$query = $_POST["query"];

if (isset($query)){
    $conn = new mysqli(FACTURA_DB_HOST, FACTURA_DB_USER, FACTURA_DB_PASS, FACTURA_DB_NAME);
    $result = $conn->query($query);
    if ($result === true){
        echo json_encode([
            'success' => true,
            'data' => true
        ]);
    }
    else if (empty($conn->error)){
        $myArray = array();
        while($row = $result->fetch_assoc()) {
            $myArray[] = $row;
        }
        $result->close();
        echo json_encode([
            'success' => true,
            'data' => $myArray
        ]);
    }
    else {
        echo json_encode([
            'success' => false,
            'message' => "Error en la consulta a la base de datos:
                          <br><br>{$conn->error}<br><br>
                          <b>Consulta:</b>
                          <br>{$query}"
        ]);
    }

    $conn->close();
    return;
}

echo json_encode([
    'success' => false,
    'message' => 'Error en la solicitud enviada.'
]);