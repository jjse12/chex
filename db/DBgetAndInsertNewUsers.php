<?php
    require_once("db_vars.php");
    require_once("server_db_vars.php");
	$server = new mysqli(SERVER_DB_HOST, SERVER_DB_USER, SERVER_DB_PASS, SERVER_DB_NAME);
	$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$query = "SELECT COUNT(*) FROM users";
    $result = $server->query($query);
    $cantServer = mysqli_fetch_row($result)[0];
    $result->free();
    $result = $conn->query("SELECT COUNT(*) FROM cliente");
    $cantLocal = mysqli_fetch_row($result)[0];
    $result->free();
    $cantNuevos = $cantServer-$cantLocal;
    //echo "Diferencia: $cantNuevos \r\n";
    if ($cantNuevos == 0){
    	echo "SINCRONIZADAS";
    }
    else{
	    $query = "SELECT user_number_id FROM users ORDER BY user_number_id LIMIT ".($cantServer-$cantNuevos).", 1";
	    $idMenor = mysqli_fetch_row($server->query($query))[0];
	    //echo "idMenor: $idMenor \r\n";

	    $query = "SELECT * FROM users WHERE user_number_id >= $idMenor ORDER BY user_number_id ASC";
	    $result = $server->query($query);
	    $values = "";
	    if ($result){
		    while($row = mysqli_fetch_row($result)){
		    	$valor = "(";
		    	$cont = 0;
		        foreach($row as $cell){
		        	switch($cont){
		        		case 0:
		        		case 5:
		        		case 6:
		        		case 12:
		        			$valor .= "$cell,";
		        		break;
		        		default:
		        		$valor .= "'$cell',";
		        	}
		        	$cont++;
		        }
		        $valor = substr($valor, 0, strlen($valor)-1) . "), ";
		        $values .= $valor;
		    }   
		    $values = substr($values, 0, strlen($values)-2);
		    $result->free();
		    $query = "INSERT IGNORE INTO cliente VALUES $values;";
		    $result = $conn->query($query);
		    if ($result){ 
		    	if ($conn->affected_rows == $cantNuevos)
		    		echo "EXITO: $cantNuevos";
		    	else
		    		echo "INCOMPLETO: " . $cantNuevos . "@" . $conn->affected_rows;
		    }
		    else
		    	echo "¡ERROR! - $conn->error";
		}
		else 
			echo "¡ERROR! - $conn->error";
	}
	$server->close();
    $conn->close();
?>