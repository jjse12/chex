<?php
header('Content-Type: text/html');
require_once("../db/db_vars.php");
require_once('../classes/CosteadorPaquetes.php');

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

$costeador = new CosteadorPaquetes($infoPaquetes);

$query = "SELECT tarifa, tarifa_express FROM cliente WHERE cid = '$uid'";
$result = $conn->query($query);
if (!empty($result) && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $costeador->setTarifaEstandar(floatval($row['tarifa']));
    $costeador->setTarifaExpress(floatval($row['tarifa_express']));
}

$costeador->setPagoTarjeta($pagoTarjeta);

$tableData = $costeador->costear();
$totales = $tableData['totales'];
$invalidPaquetes = $tableData['invalid_paquetes'];
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
        <?php foreach ($tableData['paquetes'] as $paquete): ?>
            <tr class="<?= array_search($paquete['tracking'], $invalidPaquetes) !== false ? 'invalid-paquete-express' : ''?>">
                <th class="text-center"><?= $paquete['num'] ?></th>
                <th class="text-center"><?= $paquete['servicio'] ?></th>
                <th class="text-center"><?= $paquete['tracking'] ?></th>
                <th class="text-center"><?= $paquete['guide_number'] ?></th>
                <th class="text-center"><?= $paquete['libras'] ?> lbs</th>
                <th class="text-center"><?=
                    $paquete['servicio'] === 'Express' ? (
                        empty($paquete['precio_fob']) ?
                            '<i class="fa fa-asterisk"></i>' : ('$ ' . number_format($paquete['precio_fob'], 2))
                    ) : 'N/A' ?>
                </th>
                <th class="text-center"><?=
                    $paquete['servicio'] === 'Express' ? (
                        empty($paquete['arancel']) ?
                            '<i class="fa fa-asterisk"></i>' : ((100*floatval($paquete['arancel'])) . '%')
                    ) : 'N/A' ?>
                </th>
                <th class="text-center"><?= $paquete['celulares'] ?></th>
                <th class="text-center">Q <?= number_format($paquete['cobro_celulares'], 2) ?></th>
                <th class="text-center">Q <?= number_format($paquete['cobro_extra'], 2) ?></th>
                <?php if ($pagoTarjeta) :?>
                    <th class="text-center"><?=
                        $paquete['servicio'] === 'Express' && empty($paquete['cobro_tarjeta']) ?
                        '<i class="fa fa-asterisk"></i>' : ('Q ' . number_format($paquete['cobro_tarjeta'], 2)) ?>
                    </th>
                <?php endif ?>
                <th class="text-center" title="<?= $paquete['chex_info'] ?? '' ?>"><?=
                    $paquete['servicio'] === 'Express' && empty($paquete['precio_fob']) ?
                        '<i class="fa fa-asterisk"></i>' : ('Q ' . number_format($paquete['chex'], 2)) ?>
                </th>
                <th class="text-center" title="<?= $paquete['impuestos_info'] ?? '' ?>"><?=
                    $paquete['servicio'] === 'Express' ? (
                        empty($paquete['impuestos']) ?
                            '<i class="fa fa-asterisk"></i>' : ('Q ' . number_format($paquete['impuestos'], 2))
                    ) : 'N/A'?>
                </th>
                <th class="text-center"><?=
                    $paquete['servicio'] === 'Express' && empty($paquete['total']) ?
                        '<i class="fa fa-asterisk"></i>' : ('Q ' . number_format($paquete['total'], 2)) ?>
                </th>
            </tr>
        <?php endforeach ?>
    </tbody>
    <tfoot>
        <tr class="mt-2 mb-2"><th class="text-left" colspan="<?= $pagoTarjeta ? '14' : '13' ?>">&nbsp;</th></tr>
        <tr class="mt-2 mb-2 horizontal-borders"><th class="text-left" colspan="<?= $pagoTarjeta ? '14' : '13' ?>">Resumen:</th></tr>
        <tr>
            <th class="text-left" colspan="2"><?= $totales['paquetes'] ?> paquetes</th>
            <th class="text-right" colspan="3"><?= $totales['libras'] ?> libras</th>
            <th colspan="2"></th>
            <th class="text-center"><?= $totales['celulares'] ?></th>
            <th class="text-center">Q<?= number_format($totales['cobro_celulares'], 2) ?></th>
            <th class="text-center">Q<?= number_format($totales['cobros_extras'], 2) ?></th>
            <?php if ($pagoTarjeta) :?>
                <th class="text-center">Q<?= number_format($totales['cobro_tarjeta'], 2) ?></th>
            <?php endif ?>
            <th class="text-center">Q<?= number_format($totales['chex'], 2) ?></th>
            <th class="text-center">Q<?= number_format($totales['impuestos'], 2) ?></th>

            <th id="th-total" data-total="<?= $totales['total'] ?>" class="text-center">Q <?= number_format($totales['total'], 2) ?></th>
        </tr>
    </tfoot>
</table>
<?php if (!empty($invalidPaquetes)): ?>
    <div id="restringir-entrega" style="display: none;"></div>
<?php endif ?>