<?php
    require_once("db_vars.php");
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $query = "SELECT C.rcid, fecha, tracking, uid, uname, libras, plan FROM carga C JOIN registro_carga R ON C.rcid = R.rcid WHERE C.estado IS NULL";
    $result = $conn->query($query);
    $res = "[";
    while($row = mysqli_fetch_row($result))
    {   
        $i = 0;
        $res .= "[";
        foreach($row as $cell){
            if ($i == 5)
                $res .= $cell.",";
            else $res .= "\"" . $cell ."\",";
            $i = $i+1;
        }
        $res = substr($res, 0, strlen($res)-1)."],";
    }   
    $res = substr($res, 0, strlen($res)-1)."]";
    echo $res;
?>