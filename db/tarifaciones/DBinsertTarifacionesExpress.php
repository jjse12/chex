<?php
header('Content-Type: application/json;charset=utf-8');
require_once("../db_vars.php");

try {
$data = $_POST['data'] ?? $_GET['data'];
$cambioDolar = $_POST['cambioDolar'] ?? $_GET['cambioDolar'];

if (!is_numeric($cambioDolar)){
    echo json_encode([
        'success' => false,
        'message' => "El tipo de cambio de dolar es incorrecto!",
    ]);
    return;
}

$strRows = explode("\n", $data);
foreach ($strRows as $strRow){
    if (!empty($strRow)){
        $row = explode("\t", $strRow);
        $guideNumber = $row[0];
        $precioFob = str_replace("$", "", $row[1]);
        $arancel = str_replace("%", "/100", $row[2]);
        $poliza = $row[3];
        $fechaPoliza = $row[4];
        $insertQueries[] = "INSERT INTO tarifacion_paquete_express(tracking, precio_fob, arancel, poliza, cambio_dolar, fecha_poliza) VALUES ((SELECT tracking FROM paquete WHERE guide_number = $guideNumber), $precioFob, $arancel, '$poliza', $cambioDolar, '$fechaPoliza');";
    }
}

$toInsertCount = count($insertQueries);
$insertedCount = 0;
$failedQueriesData = [];
$guideNumberPattern = "(?<guide_number>\\d+)";
$guideNumberConditionPattern = "guide_number = $guideNumberPattern\)";
$pattern = "/^.*$guideNumberConditionPattern.*$/m";

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$insertedTarifacionesGuideNumbers = [];

foreach ($insertQueries as $insertQuery) {
    $matches = [];
    preg_match($pattern, $insertQuery, $matches);
    if ($conn->query($insertQuery)) {
        $insertedCount++;
        $insertedTarifacionesGuideNumbers[] = $matches['guide_number'];
    }
    else {
        $failedQueriesData[] = [
            'guideNumber' => $matches['guide_number'],
            'query' => $insertQuery,
            'error' => $conn->error
        ];
    }
}

if ($insertedCount === count($insertQueries)) {
    echo json_encode([
        'success' => true,
        'data' => [
            'insertedTarifacionesGuideNumbers' => $insertedTarifacionesGuideNumbers,
        ]
    ]);
    exit;
}

$remaining = $toInsertCount - $insertedCount;
echo json_encode([
    'success' => false,
    'message' => "No se pudieron importar $remaining tarifaciones de las $toInsertCount tarifaciones ingresadas",
    'data' => [
        'failedQueriesData' => $failedQueriesData,
        'insertedTarifacionesGuideNumbers' => $insertedTarifacionesGuideNumbers
    ]
]);
} catch (Exception $e){
    header("HTTP/1.1 500 Internal Server Error");
    echo $e;
}