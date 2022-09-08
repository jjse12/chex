<?php
header('Content-Type: application/json;charset=utf-8');
require_once("db_vars.php");
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$query = "SELECT * FROM vendedor;";
$result = $conn->query($query);
if (isset($result) && $result !== false){
    $data = array();
    while($row = mysqli_fetch_assoc($result)){
        $data[] = $row;
    }
    echo json_encode([
        'data' => $data
    ]);;
    exit;
}

$result->close();
$conn->close();