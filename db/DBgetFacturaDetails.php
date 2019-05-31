<?php

header('Content-Type: application/json;charset=utf-8');
require_once('factura_db_vars.php');
$fid = $_POST['facturaId'];
if (isset($fid)){
    $conn = new mysqli(FACTURA_DB_HOST, FACTURA_DB_USER, FACTURA_DB_PASS, FACTURA_DB_NAME);
    $query = "SELECT * FROM factura_logistica WHERE fid = {$fid}";
    $result = $conn->query($query);
    if (isset($result) && $result !== false) {
        $logistica = mysqli_fetch_assoc($result);
        if (isset($logistica)){
            $logistica['miami_received'] = (int) $logistica['miami_received'];
        }

        $query = "SELECT * FROM factura_seguimiento WHERE fid = {$fid} ORDER BY date_created DESC";
        $result = $conn->query($query);
        if (isset($result) && $result !== false) {
            $seguimiento = [];
            while ($row = mysqli_fetch_assoc($result)){
                $seguimiento[] = $row;
            }

            echo json_encode([
                'success' => true,
                'message' => null,
                'data' => [
                    'logistica' => $logistica,
                    'seguimiento' => $seguimiento
                ]
            ]);
        }
        else{
            echo json_encode([
                'success' => false,
                'message' => "Error en la consulta a la base de datos:
                              <br><br>{$conn->error}<br><br>
                              <b>Consulta:</b>
                              <br>{$query}"
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
}
else {
    echo json_encode([
        'success' => false,
        'message' => 'Error en la solicitud enviada.'
    ]);
}
