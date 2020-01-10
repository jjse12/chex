<?php
header('Content-Type: text/html');
require_once("db/utils.php");
require_once("db/db_vars.php");
require_once("db/server_db_vars.php");

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$uid = $_POST['uid'];
$trackings = $_POST['trackings'];
$pagoTarjeta = $_POST['pagoTarjeta'] == 'true';
$query = " SET @row_number = 0; 
    SELECT (@row_number:=@row_number + 1) AS num, servicio, p.tracking, guide_number, libras, celulares, cobro_extra , t.precio_fob, t.arancel, t.tarifa_especial
    FROM paquete p LEFT JOIN tarifacion_paquete_express t ON p.tracking = t.tracking
    WHERE p.uid = '$uid' AND p.tracking IN ('" . implode('\',\'', $trackings) . "')";

$infoPaquetes = [];
if ($conn->multi_query($query) /*!empty($result) && $result->num_rows > 0*/) {
    $conn->next_result();
    $result = $conn->store_result();
    while($row = $result->fetch_assoc()) {
        $formattedRow = [];
        foreach ($row as $key => $value) {
            $formattedRow[$key] = utf8_encode($value);
        }
        $infoPaquetes[] = $formattedRow;
    }
}

$tarifaEstandarCliente = 60;

$query = "SELECT tarifa, tarifa_express FROM cliente WHERE cid = '$uid'";
$result = $conn->query($query);
if (!empty($result) && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $tarifaEstandarCliente = floatval($row['tarifa']);
    $tarifaExpressCliente = floatval($row['tarifa_express']);
}

$cantPaquetes = count($infoPaquetes);
$totalLibras = 0;
$totalCelulares = 0;
$totalCobroCelulares = 0;
$totalCobrosExtras = 0;
$totalCobroTarjeta = 0;
$totalChex = 0;
$totalImpuestos = 0;
$total = 0;
$hasInvalidaPaquete = false;
$coeficientesFetched = false;

foreach ($infoPaquetes as &$infoPaquete) {
    $celulares = intval($infoPaquete['celulares']);
    $cobroExtra = floatval($infoPaquete['cobro_extra']);
    $infoPaquete['cobro_celulares'] = $celulares * 100;

    $totalLibras += $infoPaquete['libras'];
    $totalCelulares += $celulares;
    $totalCobroCelulares += $infoPaquete['cobro_celulares'];
    $totalCobrosExtras += $cobroExtra;
    $totalPaquete = $infoPaquete['cobro_celulares'] + $cobroExtra;

    $infoPaquete['invalid'] = false;

    if ($infoPaquete['servicio'] === 'Express') {
        if (empty($infoPaquete['precio_fob']) || empty($infoPaquete['arancel'])){
            $infoPaquete['precio_fob'] = $infoPaquete['arancel'] = '';
            $infoPaquete['chex'] = $infoPaquete['impuestos'] = $infoPaquete['total'] = '<i class="fa fa-asterisk"></i>';
            if ($pagoTarjeta) {
                $infoPaquete['cobro_tarjeta'] = '<i class="fa fa-asterisk"></i>';
            }
            $infoPaquete['invalid'] = true;
            $hasInvalidaPaquete = true;
        }
        else {
            if (!$coeficientesFetched){
                $serverConn = new mysqli(SERVER_DB_HOST, SERVER_DB_USER, SERVER_DB_PASS, SERVER_DB_NAME);
                $query = "SELECT tarifa, desaduanaje, iva, seguro, cambio_dolar FROM cotizador_express_coeficientes WHERE fecha_desactivacion IS NULL";
                $res = $serverConn->query($query);
                if (!empty($res) && $res->num_rows > 0) {
                    $row = $res->fetch_assoc();
                    $tarifaFetched = floatval($row['tarifa']);
                    $desaduanaje = floatval($row['desaduanaje']);
                    $iva = floatval($row['iva']);
                    $seguro = floatval($row['seguro']);
                    $cambioDolar = floatval($row['cambio_dolar']);

                }
                $coeficientesFetched = true;
            }

            if (!isset($tarifaFetched)) {
                if ($pagoTarjeta) {
                    $infoPaquete['cobro_tarjeta'] = '';
                }
                $infoPaquete['chex'] = '';
                $infoPaquete['impuestos'] = '';
                $infoPaquete['total'] = '';
            }
            else {
                $tarifa = !empty($infoPaquete['tarifa_especial']) ? $infoPaquete['tarifa_especial'] :
                    (!empty($tarifaExpressCliente) ? $tarifaExpressCliente : $tarifaFetched);

                $cotizacion = getCotizacionExpress($tarifa, $infoPaquete['libras'], $infoPaquete['precio_fob'],
                    $infoPaquete['arancel'], $desaduanaje, $iva, $seguro, $cambioDolar);
                $infoPaquete['chex'] = $cotizacion['chex'];
                $infoPaquete['chexInfo'] = $cotizacion['chexInfo'];
                $infoPaquete['impuestos'] = $cotizacion['impuestos'];
                $infoPaquete['impuestosInfo'] = $cotizacion['impuestosInfo'];

                $totalPaquete += $cotizacion['total'];
                if ($pagoTarjeta) {
                    $infoPaquete['cobro_tarjeta'] = $totalPaquete * 0.07;
                    $totalCobroTarjeta += $infoPaquete['cobro_tarjeta'];
                    $totalPaquete += $infoPaquete['cobro_tarjeta'];
                }

                $infoPaquete['total'] = $totalPaquete;
                $totalChex += $infoPaquete['chex'];
                $totalImpuestos += $infoPaquete['impuestos'];
            }
            $infoPaquete['arancel'] = (100*floatval($infoPaquete['arancel'])) . '%';
        }
    }
    else if ($infoPaquete['servicio'] === 'Estándar' || $infoPaquete['servicio'] === 'EstÃ¡ndar') {
        $infoPaquete['precio_fob'] = null;
        $infoPaquete['arancel'] = null;
        $infoPaquete['impuestos'] = null;

        $costoChex = $tarifaEstandarCliente * $infoPaquete['libras'];
        $infoPaquete['chex'] = $costoChex;

        $totalChex += $costoChex;
        $totalPaquete += $costoChex;

        if ($pagoTarjeta) {
            $infoPaquete['cobro_tarjeta'] = 8 * $infoPaquete['libras'];
            $totalCobroTarjeta += $infoPaquete['cobro_tarjeta'];
            $totalPaquete += $infoPaquete['cobro_tarjeta'];
        }

        $infoPaquete['total'] = $totalPaquete;
    }
    unset($infoPaquete['tarifa_especial']);

    $total += $totalPaquete;
}
unset($infoPaquete);
?>

<table id="table-entrega-mercaderia" class="display compact" width="100%" cellspacing="0">
    <thead>
    <tr>
        <th></th>
        <th class="text-center" colspan="<?= $pagoTarjeta ? '8' : '7' ?>" style="background-color: #ffdebe"><span>Datos del Paquete</span></th>
        <th class="text-center" colspan="<?= $pagoTarjeta ? '6' : '5' ?>" style="background-color: #c6ddff"><span>Costos del Paquete</span></th>
    </tr>
    <tr>
        <th class="text-center"><span style="color:black">No.</span></th>
        <th class="text-center"><span style="color:black">Servicio</span></th>
        <th class="text-center"><span style="color:black">Tracking</span></th>
        <th class="text-center"><span style="color:black">No. Guía</span></th>
        <th class="text-center"><span style="color:black">Peso</span></th>
        <th class="text-center"><span style="color:black">Fob</span></th>
        <th class="text-center"><span style="color:black">Arancel</span></th>
        <th class="text-center"><span style="color:black">Celulares</span></th>
        <th class="text-center"><span style="color:black">Cobro Celulares</span></th>
        <th class="text-center"><span style="color:black">Extras</span></th>
        <?php if ($pagoTarjeta) :?>
            <th class="text-center"><span style="color:black">Tarjeta de C.</span></th>
        <?php endif ?>
        <th class="text-center"><span style="color:black">Servicios</span></th>
        <th class="text-center"><span style="color:black">Impuestos</span></th>
        <th class="text-center"><span style="color:black">Total</span></th>
        <th></th>
    </tr>
    <tr class="mt-2 mb-2"><th class="text-left" colspan="<?= $pagoTarjeta ? '14' : '13' ?>">&nbsp;</th></tr>
    </thead>
    <tbody>
        <?php foreach ($infoPaquetes as $infoPaquete): ?>
            <tr class="<?= $infoPaquete['invalid'] ? 'invalid-paquete-express' : ''?>">
                <th class="text-center"><?= $infoPaquete['num'] ?></th>
                <th class="text-center"><?= $infoPaquete['servicio'] ?></th>
                <th class="text-center"><?= $infoPaquete['tracking'] ?></th>
                <th class="text-center"><?= $infoPaquete['guide_number'] ?></th>
                <th class="text-center"><?= $infoPaquete['libras'] ?> lbs</th>
                <th class="text-center"><?= $infoPaquete['precio_fob'] === null ? 'N/A' :
                    ( $infoPaquete['precio_fob'] === '' ? '<i class="fa fa-asterisk"></i>' :
                        '$ ' . $infoPaquete['precio_fob'])?>
                </th>
                <th class="text-center"><?= $infoPaquete['arancel'] === null ? 'N/A' :
                    ($infoPaquete['arancel'] === '' ? '<i class="fa fa-asterisk"></i>' :
                        $infoPaquete['arancel'])?>
                </th>
                <th class="text-center"><?= $infoPaquete['celulares'] ?></th>
                <th class="text-center">Q <?= $infoPaquete['cobro_celulares'] ?></th>
                <th class="text-center">Q <?= $infoPaquete['cobro_extra'] ?></th>
                <?php if ($pagoTarjeta) :?>
                    <th class="text-center"><?= !is_numeric($infoPaquete['cobro_tarjeta']) ? $infoPaquete['cobro_tarjeta'] :
                        'Q ' . number_format($infoPaquete['cobro_tarjeta'], 2) ?></th>
                <?php endif ?>
                <th class="text-center" title="<?= $infoPaquete['chexInfo'] ?? '' ?>"><?= !is_numeric($infoPaquete['chex']) ? $infoPaquete['chex'] :
                    'Q ' . number_format($infoPaquete['chex'], 2) ?></th>
                <th class="text-center" title="<?= $infoPaquete['impuestosInfo'] ?? '' ?>"><?= $infoPaquete['impuestos'] === null ? 'N/A' :
                    (!is_numeric($infoPaquete['impuestos']) ? $infoPaquete['impuestos'] :
                        'Q ' . number_format($infoPaquete['impuestos'], 2))?></th>
                <th class="text-center"><?= !is_numeric($infoPaquete['total']) ? $infoPaquete['total'] :
                    'Q ' . number_format($infoPaquete['total'], 2) ?></th>
            </tr>
        <?php endforeach ?>
    </tbody>
    <tfoot>
        <tr class="mt-2 mb-2"><th class="text-left" colspan="<?= $pagoTarjeta ? '14' : '13' ?>">&nbsp;</th></tr>
        <tr class="mt-2 mb-2 horizontal-borders"><th class="text-left" colspan="<?= $pagoTarjeta ? '14' : '13' ?>">Resumen:</th></tr>
        <tr>
            <th class="text-left" colspan="2"><?= $cantPaquetes ?> paquetes</th>
            <th class="text-right" colspan="3"><?= $totalLibras ?> libras</th>
            <th colspan="2"></th>
            <th class="text-center"><?= $totalCelulares ?></th>
            <th class="text-center">Q <?= number_format($totalCobroCelulares, 2) ?></th>
            <th class="text-center">Q <?= number_format($totalCobrosExtras, 2) ?></th>
            <?php if ($pagoTarjeta) :?>
                <th class="text-center">Q <?= number_format($totalCobroTarjeta, 2) ?></th>
            <?php endif ?>
            <th class="text-center">Q <?= number_format($totalChex, 2) ?></th>
            <th class="text-center">Q <?= number_format($totalImpuestos, 2) ?></th>

            <th id="th-total" data-total="<?= $total ?>" class="text-center">Q <?= number_format($total, 2) ?></th>
        </tr>
    </tfoot>
</table>
<?php if ($hasInvalidaPaquete): ?>
    <div id="restringir-entrega" style="display: none;"></div>
<?php endif ?>