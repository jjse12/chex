<?php
header('Content-Type: application/json;charset=utf-8');
require_once("db_vars.php");

date_default_timezone_set('America/Guatemala');
$date = date("Y-m-d H:i:s");

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$query = "INSERT INTO entrega VALUES ('$date', '{$_POST['p']}', '{$_POST['ui']}', '{$_POST['un']}', " .
    "'{$_POST['to']}', '{$_POST['lbs']}', NULL, NULL, '{$_POST['m']}', '{$_POST['r']}', " .
    "{$_POST['des']}, NULL, NULL, '{$_POST['pl']}', '{$_POST['table']}');";
if ($conn->query($query)){
    $output = [
        'success' => true,
        'data' => [
            'date' => $date
        ]
    ];
    $trackings = $_POST['trackings'];
    $query = "UPDATE paquete SET estado = '$date' WHERE tracking IN ('" . implode("','", $trackings) . "')";
    if (!$conn->query($query)){
        $output['message'] = 'Ocurrió un error al intentar remover del inventario uno(s) de los paquetes de esta entrega. ' .
            'El servidor indicó el siguiente error: ' . $conn->error;
    }
    echo json_encode($output);
}
else{
    echo json_encode([
        'success' => false,
        'message' => 'Ocurrió un error al intentar crear la boleta de entrega. ' .
            'El servidor indicó el siguiente error: <i><br>' . $conn->error . '</i>'
    ]);
}
exit;