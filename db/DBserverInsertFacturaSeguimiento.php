<?php

header('Content-Type: application/json;charset=utf-8');
require_once('factura_db_vars.php');
$facturaId = $_POST['facturaId'];
$note = $_POST['note'];
$creator = $_POST['creator'];
if (isset($facturaId) && isset($note) && isset($creator)){
    $conn = new mysqli(FACTURA_DB_HOST, FACTURA_DB_USER, FACTURA_DB_PASS, FACTURA_DB_NAME);
    date_default_timezone_set('America/Guatemala');
    $dateCreated = date("Y-m-d H:i:s");
    $query = "INSERT INTO factura_seguimiento ( fid, creator, note, date_created) VALUES ( {$facturaId} , '{$creator}', '{$note}', '{$dateCreated}');";
    $result = $conn->query($query);
    if ($result === true){
        $query = "SELECT * FROM factura_seguimiento WHERE fid = {$facturaId} ORDER BY date_created DESC";
        $result = $conn->query($query);
        if (isset($result) && $result !== false) {
            $seguimientos = [];
            while ($seguimiento = mysqli_fetch_assoc($result)){
                $seguimientos[] = $seguimiento;
            }

            echo json_encode([
                'success' => true,
                'data' => $seguimientos
            ]);
        }
        else {
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
