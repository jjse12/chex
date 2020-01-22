<?php
header('Content-Type: text/html');
require_once("../db/db_vars.php");
require_once('../classes/CosteadorPaquetes.php');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$uid = $_POST['uid'] ?? '';
$wholeInventory = ($_POST['inventory'] ?? '') == 'true';
$trackings = $_POST['trackings'] ?? [];
$guideNumbers = $_POST['guideNumbers'] ?? [];
$pagoTarjeta = ($_POST['pagoTarjeta'] ?? '') == 'true';
$isEntrega = ($_POST['isEntrega'] ?? 'true') != 'false';

$query = " SET @row_number = 0; 
    SELECT (@row_number:=@row_number + 1) AS num, servicio, p.tracking, guide_number, libras, celulares, cobro_extra,
    t.precio_fob, t.arancel, t.tarifa_especial as tarifa_express_especial, c.tarifa as tarifa_estandar, c.tarifa_express
    FROM paquete p LEFT JOIN tarifacion_paquete_express t ON p.tracking = t.tracking
    LEFT JOIN cliente c on p.uid = c.cid COLLATE utf8_unicode_ci ";

if ($wholeInventory) {
    $whereClause = 'WHERE p.estado IS NULL';
}
else {
    $whereClause = !empty($trackings) ?
        ("WHERE p.tracking IN ('" . implode('\',\'', $trackings) . "')") :
            (!empty($guideNumbers) ?
        ("WHERE p.guide_number IN ('" . implode('\',\'', $guideNumbers) . "')") : "WHERE 0");
}

$query .= $whereClause;

if (!empty($uid)){
    $query .= " AND p.uid = '$uid'";
}

$infoPaquetes = [];
if ($conn->multi_query($query)) {
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

$costeador->setPagoTarjeta($pagoTarjeta);
$costeador->setIsEntrega($isEntrega);

$tableData = $costeador->costear();
$totales = $tableData['totales'];
$invalidPaquetes = $tableData['invalid_paquetes'];

?>
<table <?= $isEntrega ? '' : 'style="font-size: 9px;"' ?> id="table-entrega-mercaderia" class="display compact" width="100%" cellspacing="0">
    <thead>
        <?php if ($isEntrega) :?>
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
            </tr>
            <tr class="mt-2 mb-2"><th style="text-align: left;" colspan="<?= $pagoTarjeta ? '14' : '13' ?>">&nbsp;</th></tr>
        <?php else :?>
            <tr style="font-size: 10px;">
                <th></th>
                <th colspan="6" style="background-color: #ffdebe; text-align: center;"><span>Datos del Paquete</span></th>
                <th colspan="3" style="background-color: #c6ddff; text-align: center;"><span>Costos del Paquete</span></th>
            </tr>
            <tr style="font-size: 9.6px;">
                <th style="text-align: left;"><span style="color:black">No.</span></th>
                <th style="text-align: center;"><span style="color:black">Servicio</span></th>
                <th style="text-align: center;"><span style="color:black">Tracking</span></th>
                <th style="text-align: center;"><span style="color:black">No. Guía</span></th>
                <th style="text-align: center;"><span style="color:black">Peso</span></th>
                <th style="text-align: center;"><span style="color:black">Fob</span></th>
                <th style="text-align: center;"><span style="color:black">Arancel</span></th>
                <th style="text-align: center;"><span style="color:black">Servicios</span></th>
                <th style="text-align: center;"><span style="color:black">Impuestos</span></th>
                <th style="text-align: center;"><span style="color:black">Total</span></th>
            </tr>
            <tr style="margin-bottom: 2px; margin-top: 2px;"><th>&nbsp;</th></tr>
        <?php endif ?>
    </thead>
    <tbody>
        <?php foreach ($tableData['paquetes'] as $paquete): ?>
            <?php if ($isEntrega) :?>
                <tr class="<?= array_search($paquete['tracking'], $invalidPaquetes) !== false ? 'invalid-paquete-express' : ''?>">
                    <th class="text-center"><?= $paquete['num'] ?></th>
                    <th class="text-center"><?= $paquete['servicio'] ?></th>
                    <th class="text-center"><?= $paquete['tracking'] ?></th>
                    <th class="text-center"><?= $paquete['guide_number'] ?></th>
                    <th class="text-center"><?= $paquete['libras'] ?> libras</th>
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
            <?php else :?>
                <tr style="margin-top: 2px; margin-bottom: 2px;">
                    <th style="text-align: left;"><?= $paquete['num'] ?></th>
                    <th style="text-align: center;"><?= $paquete['servicio'] ?></th>
                    <th style="text-align: center;"><?= $paquete['tracking'] ?></th>
                    <th style="text-align: center;"><?= $paquete['guide_number'] ?></th>
                    <th style="text-align: center;"><?= $paquete['libras'] ?> libras</th>
                    <th style="text-align: center;"><?= empty($paquete['precio_fob']) ?
                        '<i class="fa fa-asterisk"></i>' : ('$ ' . number_format($paquete['precio_fob'], 2)) ?>
                    </th>
                    <th style="text-align: center;"><?= empty($paquete['arancel']) ?
                        '<i class="fa fa-asterisk"></i>' : ((100*floatval($paquete['arancel'])) . '%') ?>
                    </th>
                    <th style="text-align: center;" title="<?= $paquete['chex_info'] ?? '' ?>"><?=
                        $paquete['servicio'] === 'Express' && empty($paquete['precio_fob']) ?
                            '<i class="fa fa-asterisk"></i>' : ('Q ' . number_format($paquete['chex'], 2)) ?>
                    </th>
                    <th style="text-align: center;" title="<?= $paquete['impuestos_info'] ?? '' ?>"><?=
                        empty($paquete['impuestos']) ?
                            '<i class="fa fa-asterisk"></i>' : ('Q ' . number_format($paquete['impuestos'], 2))?>
                    </th>
                    <th style="text-align: center;"><?= empty($paquete['total']) ?
                        '<i class="fa fa-asterisk"></i>' : ('Q ' . number_format($paquete['total'], 2)) ?>
                    </th>
                </tr>
            <?php endif ?>
        <?php endforeach ?>
    </tbody>
    <tfoot>
        <?php if ($isEntrega) :?>
            <tr class="mt-2 mb-2"><th class="text-left" colspan="<?= $pagoTarjeta ? '14' : '13' ?>">&nbsp;</th></tr>
            <tr class="mt-2 mb-2 horizontal-borders"><th class="text-left" colspan="<?= $pagoTarjeta ? '14' : '13' ?>">Resumen:</th></tr>
            <tr>
                <th class="text-left" colspan="2"><?= $totales['paquetes'] ?> paquetes</th>
                <th class="text-right" colspan="3"><?= $totales['libras'] ?> libras</th>
                <th colspan="2"></th>
                <th class="text-center"><?= $totales['celulares'] ?></th>
                <th class="text-center">Q <?= number_format($totales['cobro_celulares'], 2) ?></th>
                <th class="text-center">Q <?= number_format($totales['cobros_extras'], 2) ?></th>
                <?php if ($pagoTarjeta) :?>
                    <th class="text-center">Q <?= number_format($totales['cobro_tarjeta'], 2) ?></th>
                <?php endif ?>
                <th class="text-center">Q <?= number_format($totales['chex'], 2) ?></th>
                <th class="text-center">Q <?= number_format($totales['impuestos'], 2) ?></th>

                <th id="th-total" data-total="<?= $totales['total'] ?>" class="text-center">Q <?= number_format($totales['total'], 2) ?></th>
            </tr>
        <?php else :?>
            <tr><th style="text-align: left;" colspan="10">&nbsp;</th></tr>
            <tr><th style="text-align: left;" colspan="10">Resumen:</th></tr>
            <tr>
                <th style="text-align: left;" colspan="2"><?= $totales['paquetes'] ?> paquetes</th>
                <th colspan="2"></th>
                <th style="text-align: center;"><?= $totales['libras'] ?> libras</th>
                <th colspan="2"></th>
                <th style="text-align: center;">Q <?= number_format($totales['chex'], 2) ?></th>
                <th style="text-align: center;">Q <?= number_format($totales['impuestos'], 2) ?></th>
                <th id="th-total" data-total="<?= $totales['total'] ?>"
                    style="text-align: center;">Q <?= number_format($totales['total'], 2) ?>
                </th>
            </tr>
        <?php endif ?>
    </tfoot>
</table>


<?php if (!empty($invalidPaquetes) && $isEntrega): ?>
    <div id="restringir-entrega" style="display: none;"></div>
<?php endif ?>