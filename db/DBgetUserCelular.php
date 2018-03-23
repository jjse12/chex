<?php
    require_once("db_vars.php");
	$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $query = "SELECT celular FROM cliente WHERE cid = '" . $_POST["uid"] . "';";
    $result = $conn->query($query);
    $row = mysqli_fetch_row($result);
    echo $row[0];
?>