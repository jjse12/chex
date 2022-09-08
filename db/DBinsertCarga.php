<?php

header('Content-Type: application/json;charset=utf-8');
require_once("db_vars.php");

$peso = $_POST["peso"];
$data = $_POST["data"];

date_default_timezone_set('America/Guatemala');
$date = date("Y-m-d H:i:s");

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $query = "SELECT MAX(rcid) FROM carga";
    $res = $conn->query($query);
    if ($res->num_rows == 1){
        $rcid = mysqli_fetch_row($res)[0] + 1;
        $query = "INSERT INTO carga VALUES ($rcid, '$date', " . sizeof($data) . ", $peso)";
        if ($conn->query($query)) {
            $query = "INSERT INTO paquete VALUES(";
            foreach ($data as $carga){
                $query .= "'$carga[2]', '$carga[3]', '$carga[4]', $carga[5], $rcid, NULL, '', 0, '$carga[0]', $carga[1]), (";
            }
            $query = substr($query, 0, strlen($query)-3);
            if ($conn->query($query))
                echo json_encode([
                    'success' => true,
                    'data'  => [
                        'rcid'  => $rcid,
                        'date'  => $date
                    ]
                ]);
            else {
                $error = $conn->error;
                $query = "SELECT COUNT(*) FROM paquete WHERE rcid = $rcid";
                $res = $conn->query($query);
                $agregados = mysqli_fetch_row($res)[0];
                if ($agregados == 0) {
                    $query = "DELETE FROM carga WHERE rcid = $rcid";
                }
                else {
                    $query = "UPDATE carga SET total_pqts = $agregados, total_lbs = (SELECT SUM(libras) FROM paquete WHERE rcid = $rcid) WHERE rcid = $rcid";
                }
                $res = $conn->query($query);
                echo json_encode([
                    'success' => false,
                    'data'  => [
                        'rcid'  => $rcid,
                        'date'  => $date,
                        'added' => $agregados,
                        'error' => $error
                    ]
                ]);
            }
        }
        else {
            echo json_encode([
                'success' => false,
                'data' => null
            ]);
        }
    }
    else {
        echo json_encode([
            'success' => false,
            'data' => null
        ]);
    }
}
catch(Exception $exception) {
    header("HTTP/1.1 500 Internal Server Error");
    echo $exception->getMessage();
}
finally{
    $conn->close();
}
