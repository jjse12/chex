<?php

    require_once("db_vars.php");
	$starttime = microtime(true);
	$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$query = "SELECT * FROM paquete";
	$result = $conn->query($query);
	$values = "";
	if ($result){
	    while($row = mysqli_fetch_row($result))
	    	foreach($row as $cell){
	       		$valor .= "'$cell',";
	        }
	    $values .= $valor;
    }   
    $values = substr($values, 0, strlen($values)-2);
    echo $values;
    $conn->close();
    $endtime = microtime(true);
	$duration = $endtime - $starttime; //calculates total time taken
	echo "\r\n\r\nDURACION: $duration";
?>