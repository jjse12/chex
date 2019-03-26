<?php

require_once("db_vars.php");
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$result = $conn->query($_POST["query"]);
if ($result == "1")
    echo "1";
else{
    $myArray = array();
    while($row = $result->fetch_assoc()) {
        $myArray[] = $row;
    }
    $result->close();
    echo json_encode($myArray);
}
$conn->close();
