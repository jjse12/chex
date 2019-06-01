<?php

header('Content-Type: application/json;charset=utf-8');
require_once('factura_db_vars.php');
$facturaId = $_POST['facturaId'];

if (isset($facturaId)){
    $conn = new mysqli(FACTURA_DB_HOST, FACTURA_DB_USER, FACTURA_DB_PASS, FACTURA_DB_NAME);
    $query = "INSERT INTO factura_logistica ( fid ) VALUES ( {$facturaId} )";
    $result = $conn->query($query);
    if ($result === true){
        $result = $conn->query("SELECT * FROM factura_logistica WHERE fid = {$facturaId}");
        if (isset($result) && $result !== false) {
            $data = mysqli_fetch_assoc($result);

            echo json_encode([
                'success' => true,
                'data' => $data
            ]);
        }
        else{
            echo json_encode([
                'success' => true,
                'data' => true
            ]);
        }
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
