<?php
    require_once("db_vars.php");
	$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset('utf8mb4');
    $query = "SELECT * FROM cliente ORDER BY ccid ASC";
    $result = $conn->query($query);
    $myArray = array();
    while($row = $result->fetch_assoc()) {
            $myArray[] = $row;
    }
    $result->close();
    $conn->close();

    echo json_encode($myArray);
?>