<?php
header('Content-Type: application/text;charset=utf-8');
require_once("db_vars.php");


$clientId = $_POST['clientId'];
$vendedorDetails = $_POST['clientVendedorDetails'] ?? false;
$clientDetails = $_POST['clientDetails'] ?? false;

if (empty($clientId)) {
    header("HTTP/1.1 400 Bad Request");
    echo '¡No se envió el ID del cliente a modificar!';
    return;
}

if (empty($clientDetails) && empty($vendedorDetails)) {
    header("HTTP/1.1 400 Bad Request");
    echo '¡No se envió ningún dato a modificar del cliente!';
    return;
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!empty($vendedorDetails)) {
    try {
        $vendedorId = $vendedorDetails['vendedor_id'];
        $vendedorComisionLibra = $vendedorDetails['comision_libra'];
        $vendedorComisionPaquete = $vendedorDetails['comision_paquete'];
        if (empty($vendedorId) && empty($vendedorComisionLibra) && empty($vendedorComisionPaquete)){
            $query = "DELETE FROM comisiones_vendedor_cliente WHERE cliente_id = $clientId;";
        }
        else {
            $query = "
                INSERT INTO comisiones_vendedor_cliente(vendedor_id, cliente_id, comision_libra, comision_paquete)
                VALUE ('$vendedorId', '$clientId', '$vendedorComisionLibra', '$vendedorComisionPaquete')
                ON DUPLICATE KEY UPDATE
                vendedor_id = '$vendedorId', comision_libra = '$vendedorComisionLibra', comision_paquete = '$vendedorComisionPaquete';
            ";
        }
        $conn->query($query);
    } catch (Exception $exception) {
        header("HTTP/1.1 500 Internal Server Error");
        $conn->close();
        echo $exception->getMessage();
        return;
    }
}

if (!empty($clientDetails)) {

    $fieldsToUpdateQueryString = '';
    foreach ($clientDetails as $key => $value) {
        $fieldsToUpdateQueryString .= "$key = '$value',";
    }
    $fieldsToUpdateQueryString = substr($fieldsToUpdateQueryString, 0, -1);

    $query = "UPDATE cliente SET $fieldsToUpdateQueryString WHERE ccid = $clientId;";

    try {
        if (!$conn->query($query)) {
            header("HTTP/1.1 500 Internal Server Error");
            echo "Ocurrió un error inesperado al intentar modificar la información del cliente.";
        }
    } catch (Exception $exception) {
        header("HTTP/1.1 500 Internal Server Error");
        echo $exception->getMessage();
    }
}

$conn->close();



