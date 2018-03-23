<?php
    require_once("db_vars.php");
	$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $query = "SELECT * FROM cliente ORDER BY ccid ASC";
    $result = $conn->query($query);
    $myArray = array();
    while($row = $result->fetch_assoc()) {
            $myArray[] = $row;
    }
    echo json_encode($myArray);	
?>