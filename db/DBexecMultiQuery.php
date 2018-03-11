<?php
    require_once("db_vars.php");
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $result = $conn->multi_query($_POST["query"]);
    if ($result)
    	echo "1";
    else
    	echo "Error al ejecutar consulta (" . $conn->errno . ") " . $conn->error;
?>