<?php
header('Content-Type: application/json;charset=utf-8');
require_once("../db_vars.php");

$data = $_POST['data'];
$delimiter = '-DELIMITER-';

$insertionString = 'INSERT INTO tarifacion_paquete_express(tracking, precio_fob, arancel, poliza) ' .
    'VALUES ((SELECT tracking FROM paquete WHERE guide_number = ';
$allQueries = str_replace(",", '', $data);
$allQueries = $insertionString . str_replace("	$", '), ', $allQueries);
$allQueries = str_replace("%	", "/100, '", $allQueries);
$allQueries = str_replace("	", ', ', $allQueries);
$allQueries = str_replace("\n", "');" . $delimiter . $insertionString, $allQueries);
$allQueries .= "')";

$insertQueries = explode($delimiter, $allQueries);

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
