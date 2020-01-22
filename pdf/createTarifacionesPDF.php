<?php

require_once '../classes/TarifacionesPDFGenerator.php';

$table = $_POST['table'];

if (isset($table)){
    try {
        $pdfGenerator = new TarifacionesPDFGenerator($table);
        $pdfGenerator->render();
    } catch (Exception $e) {
        header("HTTP/1.1 500 Internal Server Error");
        echo "Error al procesar el pdf";
    }
}
else {
    header("HTTP/1.1 500 Internal Server Error");
    echo "Error en la solicitud enviada.";
}