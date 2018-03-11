<?php
	require_once("db_vars.php");
	$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
   	$query = "SELECT user_admin FROM admintable WHERE user_name = '".$_POST["user"]."' AND user_password = '".$_POST["pass"]."' LIMIT 1 ";
    $result = $conn->query($query);
    if ($result->num_rows == 0)
    	echo "-1";
    else{
    	//echo " - row: $row - row[0]: $row[0]";	
    	$row = mysqli_fetch_row($result);
    	echo $row[0];
    }
?>