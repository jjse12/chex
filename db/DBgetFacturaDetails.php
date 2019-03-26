<?php

header('Content-Type: application/json;charset=utf-8');
require_once("server_db_vars.php");
$fid = $_POST['facturaId'];
if (isset($fid)){
    $conn = new mysqli(SERVER_DB_HOST, SERVER_DB_USER, SERVER_DB_PASS, SERVER_DB_NAME);
    $conn->set_charset('utf8mb4');
    $query = "SELECT * FROM factura_logistica WHERE fid = {$fid}";
    $result = $conn->query($query);
    if (isset($result) && $result !== false) {
        $logistica = mysqli_fetch_assoc($result);
        if (isset($logistica)){
            $logistica['miami_received'] = (int) $logistica['miami_received'];
        }

        $query = "SELECT * FROM factura_seguimiento WHERE fid = {$fid} ORDER BY date_created ASC";
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