<?php

$table = $_POST['table'];

if (isset($table)){
    try {
        $path =  $_SERVER['DOCUMENT_ROOT'] . '/tarifaciones-ingresadas/';
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $path =  $_SERVER['DOCUMENT_ROOT'] . '/chex/tarifaciones-ingresadas/';
            $path = str_replace('/', '\\', $path);
        }

        date_default_timezone_set('America/Guatemala');
        $fileDate = date('d-m-Y_h-i-s_A');
        $fileName = "html_tarifaciones_{$fileDate}.html";
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
        echo "Error al procesar el pdf";
    }
}
else {
    header("HTTP/1.1 500 Internal Server Error");
    echo "Error en la solicitud enviada.";
}