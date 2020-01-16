<?php
header('Content-Type: application/json;charset=utf-8');
require_once("../db_vars.php");

$data = $_POST['data'];
$delimiter = '-DELIMITER-';

$insertionString = 'insert into tarifacion_paquete_express(tracking, precio_fob, arancel) ' .
    'VALUES ((select tracking from paquete where guide_number = ';
$allQueries = $insertionString . str_replace("	$", '), ', $data);
$allQueries = str_replace("	", ', ', $allQueries);
$allQueries = str_replace("%", '/100);', $allQueries);
$allQueries = str_replace("\n", $delimiter . $insertionString, $allQueries);

$insertQueries = explode($delimiter, $allQueries);

$toInsertCount = count($insertQueries);
$insertedCount = 0;
$failedQueriesData = [];
$guideNumberPattern = "(?<guide_number>\\d+)";
$guideNumberConditionPattern = "guide_number = $guideNumberPattern\)";
$pattern = "/^.*$guideNumberConditionPattern.*$/m";

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

foreach ($insertQueries as $insertQuery) {
    if ($conn->query($insertQuery)) {
        $insertedCount++;
    }
    else {
        preg_match($pattern, $insertQuery, $matches);
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
    ]);
    exit;
}

$remaining = $toInsertCount - $insertedCount;
echo json_encode([
    'success' => false,
    'message' => "No se pudieron importar $remaining tarifaciones de las $toInsertCount tarifaciones ingresadas",
    'data' => [
        'failedQueriesData' => $failedQueriesData
    ]
]);
