<?php

require_once '../classes/FacturasPDFGenerator.php';

$facturas = $_POST['facturas'];

if (isset($facturas)){
    try {
        $pdfGenerator = new FacturasPDFGenerator($facturas);
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