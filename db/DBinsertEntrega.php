<?php
    require_once("db_vars.php");
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $query = "INSERT INTO entrega VALUES ('".$_POST["d"]."', '".$_POST["p"]."', '".$_POST["ui"]."', '".$_POST["un"]."', '".$_POST["to"]."', '".$_POST["lbs"]."', '".$_POST["tar"]."', '".$_POST["st"]."', '".$_POST["m"]."', ".$_POST["r"].", ".$_POST["des"].", ".$_POST["det"].", NULL, '".$_POST["pl"]."');";
    $res = $conn->query($query);
    if ($res){
		echo $conn->affected_rows;
    }
    else{
    	echo "¡ERROR! - $conn->error";
    }
    $conn->close();
?>