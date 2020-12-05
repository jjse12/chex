<?php

$table = $_POST['boleta'];
$boletaId = $_POST['boletaId'];

if (isset($table)){
    try {
        $path =  $_SERVER['DOCUMENT_ROOT'] . '/boletas/';
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $path =  $_SERVER['DOCUMENT_ROOT'] . '/chex/boletas/';
            $path = str_replace('/', '\\', $path);
        }

        date_default_timezone_set('America/Guatemala');
        $fileName = "boleta_" . (10000 + intval($boletaId)) . ".html";
        $filePath = $path . $fileName;
        $file = fopen($filePath, "w");
        fwrite($file, $table);
        fclose($file);
        header('Content-Type: application/json;charset=utf-8');
        echo json_encode([
            'fileName' => $fileName
        ]);
    } catch (Exception $e) {
        header("HTTP/1.1 500 Internal Server Error");
        echo "Error al guardar la boleta.";
    }
}
else {
    header("HTTP/1.1 500 Internal Server Error");
    echo "Error en la solicitud enviada.";
}