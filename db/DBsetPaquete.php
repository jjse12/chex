<?php
    require_once("db_vars.php");
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $query = "UPDATE carga SET " . $_POST["set"]." WHERE " . $_POST["where"];
    $result = $conn->query($query);
    if ($result){
		echo $conn->affected_rows;
    }
    else{
    	echo "¡ERROR! - $conn->error";
    }
    $conn->close();
?>