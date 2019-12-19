<?php

function getServicesFromSqlResult(mysqli_result $result) {
    $data = [];
    while($row = mysqli_fetch_assoc($result)){
        $row['nombre'] = utf8_encode($row['nombre']);
        $row['descripcion'] = utf8_encode($row['descripcion']);
        $row['aviso'] = utf8_encode($row['aviso']);
        $data[] = $row;
    }
    return $data;
}
