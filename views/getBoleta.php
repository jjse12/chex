<?php

require_once('../classes/Boleta.php');
require_once('../classes/BoletaStorer.php');

$fecha = $_POST['fecha'] ?? '';
$cliente = $_POST['cliente'] ?? '';
$receptor = $_POST['receptor'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$direccion = $_POST['direccion'] ?? '';
$tipo = $_POST['tipo'] ?? '';
$metodoPago = $_POST['metodoPago'] ?? '';
$paquetes = $_POST['paquetes'] ?? '';
$costoPaquetes = $_POST['costoPaquetes'] ?? '';
$costoTotal = $_POST['costoTotal'] ?? '';
$comentario = $_POST['comentario'] ?? '';

if (empty($fecha) || empty($cliente) || empty($receptor) || empty($telefono) ||empty($direccion) ||
    empty($tipo) || empty($metodoPago) || empty($paquetes) || empty($costoPaquetes) || empty($costoTotal)
) {
    header("HTTP/1.1 400 Bad Request");
    echo "Error en la solicitud enviada.";
    exit;
}

header('Content-Type: application/json;charset=utf-8');

try {
    $storer = new BoletaStorer();
    $fileNames = $storer->store(new Boleta($fecha, $cliente, $receptor, $telefono, $direccion, $tipo,
        $metodoPago, $paquetes, $costoPaquetes, $costoTotal, $comentario));
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
