<?php
    require_once("db_vars.php");
    require_once("server_db_vars.php");
    require_once("../classes/CosteadorPaquetes.php");

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
    if ($cantNuevos == 0){
    	echo "SINCRONIZADAS";
        $server->close();
        $conn->close();
        return;
    }

    $query = "SELECT user_number_id FROM users ORDER BY user_number_id LIMIT ".($cantServer-$cantNuevos).", 1";
    $result = $server->query($query);
    $idMenor = mysqli_fetch_row($result)[0];
    $result->free();

    $query = "SELECT * FROM users WHERE user_number_id >= $idMenor ORDER BY user_number_id ASC";
    $result = $server->query($query);
    $values = "";
    if ($result){


        $coeficientesQuery = "SELECT tarifa, desaduanaje, seguro FROM cotizador_express_coeficientes WHERE fecha_desactivacion IS NULL";
        $coeficientesResult = $server->query($coeficientesQuery);

        $tarifa = CosteadorPaquetes::DEFAULT_TARIFA_EXPRESS;
        $desaduanaje = CosteadorPaquetes::DEFAULT_DESADUANAJE;
        $seguro = CosteadorPaquetes::DEFAULT_SEGURO;

        if (!empty($coeficientesResult) && $coeficientesResult->num_rows > 0) {
            $row = $coeficientesResult->fetch_assoc();

            $tarifa = (float)$row['tarifa'];
            $desaduanaje = (float)$row['desaduanaje'];
            $seguro = (float)$row['seguro'];
        }

        while($row = mysqli_fetch_row($result)){
            $valor = "(";
            $cont = 0;
            foreach($row as $cell){
                $cell = str_replace("'", "\\'", $cell);
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
            $valor .= $tarifa .
                ',' . $desaduanaje .
                ',' . $seguro . '), ';
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

	$server->close();
    $conn->close();
?>