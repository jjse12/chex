<?php

function getServicesFromSqlResult(mysqli_result $result) {
    $data = [];
    while($row = mysqli_fetch_assoc($result)){
        $data[] = $row;
    }
    return $data;
}
