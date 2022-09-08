<?php
header('Content-Type: application/json;charset=utf-8');
require_once("db_vars.php");
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$getLastClientSyncQuery = "SELECT fecha FROM sincronizacion_clientes ORDER BY fecha DESC LIMIT 1";
$lastClientSyncResult = $conn->query($getLastClientSyncQuery);
$lastSyncDatetime = mysqli_fetch_assoc($lastClientSyncResult)['fecha'];
$lastClientSyncResult->close();

$query = "SELECT c.*, v.vendedor_id, v.vendedor_nombre, v.vendedor_comision_paquete, v.vendedor_comision_libra
FROM cliente c left join (
    select
        v.id                 as vendedor_id,
        v.nombre             as vendedor_nombre,
        cvc.comision_paquete as vendedor_comision_paquete,
        cvc.comision_libra   as vendedor_comision_libra,
        cvc.cliente_id
   from vendedor v join comisiones_vendedor_cliente cvc on v.id = cvc.vendedor_id
) as v on c.ccid = v.cliente_id
ORDER BY ccid;";
$result = $conn->query($query);
if (isset($result) && $result !== false){
    $data = array();
    while($row = mysqli_fetch_assoc($result)){
        $data[] = $row;
    }
    echo json_encode([
        'data' => [
            'clients' => $data,
            'lastSyncDatetime' => $lastSyncDatetime,
        ]
    ]);;
    exit;
}

$result->close();
$conn->close();