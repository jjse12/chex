<?php 
    require_once("db_vars.php");
	$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $query = "SELECT ".$_POST['select']." FROM entrega WHERE ".$_POST["where"];
    $result = $conn->query($query);
    $myArray = array();
    while($row = $result->fetch_assoc()) {
            $myArray[] = $row;
    }
    echo json_encode($myArray);
?>