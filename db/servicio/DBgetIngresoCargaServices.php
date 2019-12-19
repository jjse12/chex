<?php

header('Content-Type: text/html');
require_once('servicio_db_vars.php');
require_once('utils.php');
$conn = new mysqli(SERVICIO_DB_HOST, SERVICIO_DB_USER, SERVICIO_DB_PASS, SERVICIO_DB_NAME);
$query = "
    SELECT *
    FROM servicio
    WHERE ingreso_carga = 1
    ";

$result = $conn->query($query);
if (isset($result) && $result !== false) {
    $data = getServicesFromSqlResult($result);
    if (empty($data)) {
        echo '<p class="text-center"><span class="text-color-gray">- No se encontraron servicios -</span></p>';
    }
    else {
        echo '<select id="servicio" name="servicio" class="form-control"><option value="">Selecciona un servicio</option>';
        foreach ($data as $service) {
            echo "<option value=\"{$service['nombre']}\">{$service['nombre']}<br>";
        }
        echo '</select>';
    }
}
else {
    header("HTTP/1.1 500 Internal Server Error");
}
$conn->close();