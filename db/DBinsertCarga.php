<?php
    require_once("db_vars.php");
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $query = "SELECT MAX(rcid) FROM registro_carga";
    $res = $conn->query($query);
    if ($res == 1 && $res->num_rows == 1){   
        $rcid = mysqli_fetch_row($res)[0] + 1;
        $res->close();

        $peso = $_POST["peso"];
        $date = $_POST["date"];
        $data = $_POST["data"];

        $query = "INSERT INTO registro_carga VALUES (".$rcid.", '".$date."', ".sizeof($data).",".$peso.")";
        if ($conn->query($query)) {
            $query = "INSERT INTO carga VALUES(";
            foreach ($data as $carga){
                $query .= "'".$carga[0].
                        "', '".$carga[1].
                        "', '".$carga[2].
                        "', ".$carga[3].", ".$rcid.", NULL, ''), (";
            }
            $query = substr($query, 0, strlen($query)-3);
            if ($conn->query($query))
                echo "$rcid@$date";
            else {
                $error = $conn->error;
                $query = "SELECT COUNT(*) FROM carga WHERE rcid = $rcid";
                $res = $conn->query($query);
                $agregados = mysqli_fetch_row($res)[0];
                $res->close();
                if ($agregados == 0)
                    $query = "DELETE FROM registro_carga WHERE rcid = $rcid";
                else
                    $query = "UPDATE registro_carga SET total_pqts = $agregados, total_lbs = (SELECT SUM(libras) FROM carga WHERE rcid = $rcid) WHERE rcid = $rcid";
                $res = $conn->query($query);
                echo "$agregados@$error|$rcid@$date";
            }
        }
        else
            echo "errorRegistro";
    }
    else
        echo "errorRegistro";
    $conn->close();
?>