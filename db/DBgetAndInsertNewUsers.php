<?php
header('Content-Type: application/json;charset=utf-8');
require_once("db_vars.php");
require_once("server_db_vars.php");
require_once("../classes/CosteadorPaquetes.php");

try {
	$localDB = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$serverDB = new mysqli(SERVER_DB_HOST, SERVER_DB_USER, SERVER_DB_PASS, SERVER_DB_NAME);

    $getLastClientSyncQuery = "SELECT fecha FROM sincronizacion_clientes ORDER BY fecha DESC LIMIT 1";
    $lastClientSyncResult = $localDB->query($getLastClientSyncQuery);
    $lastSyncDatetime = mysqli_fetch_assoc($lastClientSyncResult)['fecha'];
    $lastClientSyncResult->close();

    $getServerClientsInsertedAfterLastSyncDatetimeQuery = "SELECT * FROM users WHERE creation_date > '$lastSyncDatetime'";
    $serverNewClientsResult = $serverDB->query($getServerClientsInsertedAfterLastSyncDatetimeQuery);

    if (!$serverNewClientsResult)
        throw new RuntimeException($serverDB->error);

    if ($serverNewClientsResult->num_rows === 0) {
        echo json_encode(['numberOfClientsInserted' => 0]);
        return;
    }

    $coeficientesQuery = "SELECT tarifa, desaduanaje, seguro FROM cotizador_express_coeficientes WHERE fecha_desactivacion IS NULL";
    $coeficientesResult = $serverDB->query($coeficientesQuery);

    $tarifaExpress = CosteadorPaquetes::DEFAULT_TARIFA_EXPRESS;
    $desaduanaje = CosteadorPaquetes::DEFAULT_DESADUANAJE;
    $seguro = CosteadorPaquetes::DEFAULT_SEGURO;

    if (!empty($coeficientesResult) && $coeficientesResult->num_rows > 0) {
        $row = $coeficientesResult->fetch_assoc();

        $tarifaExpress = (float)$row['tarifa'];
        $desaduanaje = (float)$row['desaduanaje'];
        $seguro = (float)$row['seguro'];
    }

    $values = "";
    while($client = mysqli_fetch_assoc($serverNewClientsResult)){
        $values .= "('{$client['chex_code']}', '{$client['first_name']}', '{$client['last_name']}', '{$client['email']}', '{$client['phone']}', '{$client['secondary_phone']}',
                 '{$client['department']}', '{$client['municipality']}', '{$client['zone']}', '{$client['address']}', '{$client['nit_name']}', '{$client['nit_number']}', '{$client['birthday']}',
                 '{$client['note']}', '{$client['meet_reason']}', '{$client['gender']}', '{$client['creation_date']}', $tarifaExpress, $desaduanaje, $seguro), ";
    }
    $values = substr($values, 0, -2);
    $serverNewClientsResult->close();

    $insertNewClientsQuery = "INSERT INTO cliente(cid, nombre, apellido, email, celular, telefono_secundario,
                    departamento, municipio, zona, direccion, nit_nombre, nit_numero, cumple, comentario,
                    referencia, genero, fecha_registro, tarifa_express, desaduanaje_express, seguro)
                VALUES $values";

    $insertNewClientsResult = $localDB->query($insertNewClientsQuery);
    if (!$insertNewClientsResult) {
        throw new RuntimeException($serverDB->error);
    }

    $insertedClientsCount = $localDB->affected_rows;
    $insertNewClientSynchronizationQuery = "INSERT INTO sincronizacion_clientes(cantidad_clientes_ingresados) VALUE ($insertedClientsCount)";
    echo json_encode(['numberOfClientsInserted' => $insertedClientsCount]);

    $localDB->query($insertNewClientSynchronizationQuery);
} catch (Exception $exception) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(['errorMessage' => $exception->getMessage()]);
} finally {
    $localDB->close();
    $serverDB->close();
}
