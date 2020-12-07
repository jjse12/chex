<?php

require_once('../classes/Boleta.php');
require_once('../classes/BoletaStorer.php');

$fecha = $_GET['fecha'] ?? '';
$cliente = $_GET['cliente'] ?? '';
$receptor = $_GET['receptor'] ?? '';
$telefono = $_GET['telefono'] ?? '';
$direccion = $_GET['direccion'] ?? '';
$tipo = $_GET['tipo'] ?? '';
$metodoPago = $_GET['metodoPago'] ?? '';
$paquetes = $_GET['paquetes'] ?? '';
$costoPaquetes = $_GET['costoPaquetes'] ?? '';
$costoRuta = $_GET['costoRuta'] ?? '';
$costoTotal = $_GET['costoTotal'] ?? '';
$comentario = $_GET['comentario'] ?? '';

if (empty($fecha) || empty($cliente) || empty($receptor) || empty($telefono) ||empty($direccion) ||
    empty($tipo) || empty($metodoPago) || empty($paquetes) || empty($costoPaquetes) || empty($costoTotal)
) {
    header("HTTP/1.1 400 Bad Request");
    echo "Error en la solicitud enviada.";
    exit;
}

$paquetes = json_decode($paquetes, true);
header('Content-Type: application/json;charset=utf-8');

try {
    $storer = new BoletaStorer(new Boleta($fecha, $cliente, $receptor, $telefono, $direccion, $tipo,
        $metodoPago, $paquetes, $costoPaquetes, $costoRuta, $costoTotal, $comentario));
    $fileNames = $storer->store();
    echo json_encode([
        'success' => true,
        'data' => [
            'boletas' => $fileNames,
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => "Error al intentar generar la boleta",
        'data' => [
            'errorMessage' => $e->getMessage(),
            'stackTrace' => $e->getTraceAsString()
        ]
    ]);
    exit;
}
