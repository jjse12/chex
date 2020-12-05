<?php

$boletaId = $_GET['boletaId'] ?? '';
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
$costoTotal = $_GET['costoRuta'] ?? '';
$comentario = $_GET['comentario'] ?? '';

if (empty($boletaId) || empty($fecha) || empty($cliente) || empty($receptor) ||
    empty($telefono) || empty($direccion) || empty($tipo) || empty($metodoPago) ||
    empty($paquetes) || empty($costoPaquetes) || empty($costoRuta) || empty($costoTotal)
){
    header("HTTP/1.1 400 Bad Request");
    echo "Error en la solicitud enviada.";
    exit;
}

$paquetes = json_decode($paquetes, true);

header('Content-Type: text/html');

?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        .invoice-box {
            max-width: 760px;
            height: <?= sizeof($paquetes) < 11 ? 490 : 1015 ?>px;
            max-height: <?= sizeof($paquetes) < 11 ? 490 : 1015 ?>px;
            margin: auto;
            padding: 14px;
            border: 3px solid #bbb;
            font-size: 13px;
            line-height: 13px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 4px;
        }

        .heading {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        td.cell {
            border: 1px solid #eee;
        }

        .invoice-box table tr.total td {
            padding-top: 10px;
            border-bottom: 2px solid #aaa;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.paquete td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.paquete.last td {
            border-bottom: none;
        }

        /** RTL **/
        .rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .rtl table {
            text-align: right;
        }

        .rtl table tr td:nth-child(2) {
            text-align: left;
        }
    </style>
</head>

<body>
<div class="invoice-box">
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="2" style="padding: 2px">
                <table>
                    <tr>
                        <td style="padding-bottom: 0">
                            <img alt="Chispudito Express" style="max-width: 128px" src="/images/logo-courier-y-carga.png">
                        </td>
                        <td>
                            Boleta #<?= 10000 + intval($boletaId) ?>
                            <br>
                            <?= $fecha ?>
                            <br>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class="information">
            <td colspan="2">
                <table>
                    <tr>
                        <td>
                            <strong>Cliente:</strong> <?= $cliente ?>
                            <br>
                            <strong>Nombre:</strong> <?= $receptor ?>
                            <br>
                            <strong>Teléfono:</strong> <?= $telefono ?>
                            <br>
                            <strong>Dirección:</strong> <?= $direccion ?>
                        </td>
                        <td>
                            <strong>Tipo:</strong> <?= $tipo ?><br>
                            <strong>Forma de pago:</strong> <?= $metodoPago ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding-left: 12px;">
                <strong>Detalle de paquetes:</strong>
            </td>
        </tr>
        <tr>
            <td style="padding-left: 10px; padding-right: 12px">
                <table>
                    <tr class="heading">
                        <td>Tracking</td>
                        <td>Peso</td>
                    </tr>
                    <?php foreach ($paquetes as $paquete): ?>
                        <tr class="paquete">
                            <td class="tracking">
                                <?= $paquete['tracking'] ?>
                            </td>
                            <td class="peso">
                                <?= $paquete['peso'] ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total">
                        <td>
                            Total de paquetes: <?= sizeof($paquetes) ?>
                        </td>
                        <td>
                            Total peso: <?= array_sum(array_column($paquetes, 'peso')) ?>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="50%">
                <table>
                    <tr>
                        <td colspan="2"><strong>Costo paquetes: </strong> Q<?= $costoPaquetes ?></td>
                        <td class="heading">Total a cancelar</td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Costo ruta: </strong> Q<?= $costoRuta ?></td>
                        <td class="cell">Q<?= $costoTotal ?></td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <br><br><br><br>
                            <span>
                                <strong>Comentario:</strong> <?= $comentario ?>
                            </span>
                            <br><br><br><br><br><br><br><br><br><br><br>
                        </td>
                    </tr>
                    <tr style="position: relative">
                        <td colspan="3" style="width: 80%; margin-left: 10%; text-align: center; position: absolute; bottom: 0; border-top: #aaa solid 2px">
                            Firma de recibido
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
</body>
</html>