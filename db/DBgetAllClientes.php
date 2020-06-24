<?php
header('Content-Type: application/json;charset=utf-8');
require_once("db_vars.php");
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$query = "SELECT * FROM cliente ORDER BY ccid ASC";
$result = $conn->query($query);
if (isset($result) && $result !== false){
    $data = array();
    while($row = mysqli_fetch_assoc($result)){
        $formattedRow = [];
        foreach ($row as $key => $value) {
            $formattedRow[$key] = utf8_encode($value);
        }
        $data[] = $formattedRow;
    }
    echo json_encode([
        'data' => $data
    ]);;
    exit;
}

$result->close();
$conn->close();