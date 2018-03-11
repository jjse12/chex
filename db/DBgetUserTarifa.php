<?php
    require_once("db_vars.php");
	$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $query = "SELECT user_tarifa FROM users WHERE user_id = '" . $_POST["uid"] . "';";
    $result = $conn->query($query);
    $row = mysqli_fetch_row($result);
    echo $row[0];
?>