<?php
header('Content-Type: text/html');
require_once("../db/db_vars.php");
require_once('../classes/CosteadorPaquetes.php');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$uid = $_POST['uid'] ?? '';
$wholeInventory = ($_POST['inventory'] ?? '') == 'true';
$trackings = $_POST['trackings'] ?? [];
$guideNumbers = $_POST['guideNumbers'] ?? [];
$pagoTarjeta = !empty($_POST['tipoTarjeta']);
$tipoTarjeta = $_POST['tipoTarjeta'];

$isTarifacion = ($_POST['isTarifacion'] ?? 'false') == 'true';

$selectStatement = "
    SELECT (@row_number:=@row_number + 1) AS num, servicio, p.tracking, guide_number, libras, cobro_extra,
        ca.fecha as fecha_ingreso, t.precio_fob, t.arancel, t.poliza, t.fecha_poliza, cl.desaduanaje_express as desaduanaje,
        t.tarifa_especial as tarifa_express_especial, cl.tarifa as tarifa_estandar, cl.tarifa_express";
if ($isTarifacion){
    $selectStatement .= ", cl.cid as clientChexCode, CONCAT(cl.nombre, ' ', cl.apellido) as clientName";
}

$query = " SET @row_number = 0; 
    $selectStatement
    FROM paquete p LEFT JOIN tarifacion_paquete_express t ON p.tracking = t.tracking
    LEFT JOIN cliente cl on p.uid = cl.cid COLLATE utf8_unicode_ci
    LEFT JOIN carga ca on p.rcid = ca.rcid ";

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
$costeador->setRecargoTarjeta($tipoTarjeta);
$costeador->setIsTarifacion($isTarifacion);

$tableData = $costeador->costear();
$totales = $tableData['totales'];
$invalidPaquetes = $tableData['invalid_paquetes'];


//TODO: Continue implementation of individual functions that render corresponding tables
function renderTableEntrega($pagoTarjeta){
    $colspanCostoPaquete = $pagoTarjeta ? 11 : 10;
    $colspanHeaderFila2 = $pagoTarjeta ? 10 : 9;
    $colspanFinalHeader = $pagoTarjeta ? 18 : 17;

    return "
        <table id='table-entrega-mercaderia' class='display compact' width='100%' cellspacing='0'>
            <thead>
            <tr>
                <th></th>
                <th class='text-center' colspan='7' style='background-color: #ffdebe'><span>Datos del Paquete</span></th>
                <th class='text-center' colspan='$colspanCostoPaquete' style='background-color: #c6ddff'><span>Costos del Paquete</span></th>
            </tr>
            <tr>
                <th colspan='$colspanHeaderFila2'></th>
                <th class='text-center' colspan='4' style='background-color: #ff9e2f'><span>Servicios CHEX</span></th>
                <th class='text-center' colspan='3' style='background-color: #fffaa2'><span>Impuestos</span></th>
            </tr>
            <tr>
                <th class='text-center'><span style='color:black'>No.</span></th>
                <th class='text-center'><span style='color:black'>Servicio</span></th>
                <th class='text-center'><span style='color:black'>Tracking</span></th>
                <th class='text-center'><span style='color:black'>No. Guía</span></th>
                <th class='text-center'><span style='color:black'>Peso</span></th>
                <th class='text-center'><span style='color:black'>Fob</span></th>
                <th class='text-center'><span style='color:black'>Arancel</span></th>
                <th class='text-center'><span style='color:black'>Póliza</span></th>
                <th class='text-center'><span style='color:black'>Extras</span></th>" .
                (   $pagoTarjeta
                    ? "<th class='text-center'><span style='color:black'>Tarjeta de C.</span></th>"
                    : ''
                ) . "
                <th class='text-center'><span style='color:rgba(0,0,0,0.4)'>Peso</span></th>
                <th class='text-center'><span style='color:rgba(0,0,0,0.4)'>Desaduanaje</span></th>
                <th class='text-center'><span style='color:rgba(0,0,0,0.4)'>Seguro</span></th>
                <th class='text-center'><span style='color:black'>Total</span></th>
                <th class='text-center'><span style='color:rgba(0,0,0,0.4)'>Arancel</span></th>
                <th class='text-center'><span style='color:rgba(0,0,0,0.4)'>IVA</span></th>
                <th class='text-center'><span style='color:black'>Total</span></th>
                <th class='text-center'><span style='color:black'>Total</span></th>
            </tr>
            <tr class='mt-2 mb-2'><th style='text-align: left;' colspan='$colspanFinalHeader'>&nbsp;</th></tr>
            </thead>
        </table>
    ";
}

?>


<?php if (!$isTarifacion) :?>
    <table style="border: 1px solid #222; white-space: nowrap; font-size: 12px" id="table-entrega-mercaderia" class="display compact" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th></th>
                <th class="text-center" colspan="9" style="background-color: #ffdebe"><span>Datos del Paquete</span></th>
                <th class="text-center" colspan="<?= $pagoTarjeta ? 11 : 10 ?>" style="background-color: #c6ddff"><span>Costos del Paquete</span></th>
            </tr>
            <tr>
                <th colspan="<?= $pagoTarjeta ? 12 : 11 ?>"></th>
                <th class="text-center" colspan="4" style="background-color: #ff9e2f"><span>Servicios CHEX</span></th>
                <th class="text-center" colspan="3" style="background-color: #fffaa2"><span>Impuestos</span></th>
            </tr>
            <tr>
                <th class="text-center"><span style="color:black">No.</span></th>
                <th class="text-center"><span style="color:black">Servicio</span></th>
                <th class="text-center"><span style="color:black">Tracking</span></th>
                <th class="text-center"><span style="color:black">No. Guía</span></th>
                <th class="text-center"><span style="color:black">Peso</span></th>
                <th class="text-center"><span style="color:black">Fob</span></th>
                <th class="text-center"><span style="color:black">Arancel</span></th>
                <th class="text-center"><span style="color:black">Póliza</span></th>
                <th class="text-center"><span style="color:black">Fecha de Póliza</span></th>
                <th class="text-center"><span style="color:black">Valor BI</span></th>
                <th class="text-center" style="border-left: 1px solid #222;"><span style="color:black">Extras</span></th>
                <?php if ($pagoTarjeta) :?>
                    <th class="text-center"><span style="color:black">Tarjeta de C.</span></th>
                <?php endif ?>
                <th class="text-center"><span style="color:rgba(0,0,0,0.4)">Peso</span></th>
                <th class="text-center"><span style="color:rgba(0,0,0,0.4)">Desaduanaje</span></th>
                <th class="text-center"><span style="color:rgba(0,0,0,0.4)">Seguro</span></th>
                <th class="text-center"><span style="color:black">Total</span></th>
                <th class="text-center" style="border-left: 1px solid #222;"><span style="color:rgba(0,0,0,0.4)">DAI</span></th>
                <th class="text-center"><span style="color:rgba(0,0,0,0.4)">IVA</span></th>
                <th class="text-center"><span style="color:black">Total</span></th>
                <th class="text-center" style="border-left: 1px solid #222;"><span style="color:black">Total</span></th>
            </tr>
            <tr class="mt-2 mb-2">
                <th colspan="10"></th>
                <th class="text-center" colspan="<?= $pagoTarjeta ? 6 : 5 ?>" style="border-left: 1px solid #222;">&nbsp</th>
                <th class="text-center" colspan="3" style="border-left: 1px solid #222;">&nbsp</th>
                <th class="text-center" style="border-left: 1px solid #222;">&nbsp</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tableData['paquetes'] as $paquete): ?>
                <tr class="<?= array_search($paquete['tracking'], $invalidPaquetes) !== false ? 'invalid-paquete-express' : ''?>">
                    <th class="text-center"><?= $paquete['num'] ?></th>
                    <th class="text-center"><?= $paquete['servicio'] ?></th>
                    <th class="text-center"><?= $paquete['tracking'] ?></th>
                    <th class="text-center"><?= $paquete['guide_number'] ?></th>
                    <th class="text-center"><?= $paquete['libras'] ?> lb</th>
                    <?php if ($paquete['servicio'] === 'Express') :?>
                        <th class="text-center">
                            <?= empty($paquete['precio_fob']) ?
                                '<i class="fa fa-asterisk"></i>' :
                                ('$ ' . number_format($paquete['precio_fob'], 2))
                            ?>
                        </th>
                        <th class="text-center">
                            <?= empty($paquete['arancel']) ?
                                '<i class="fa fa-asterisk"></i>' :
                                ((100*floatval($paquete['arancel'])) . '%')
                            ?>
                        </th>
                        <th class="text-center">
                            <?= empty($paquete['poliza']) ?
                                '<i class="fa fa-asterisk"></i>' :
                                $paquete['poliza']
                            ?>
                        </th>
                        <th class="text-center">
                            <?= empty($paquete['fecha_poliza']) ?
                                '<i class="fa fa-asterisk"></i>' :
                                $paquete['fecha_poliza']
                            ?>
                        </th>
                        <th class="text-center">
                            <?= empty($paquete['costos_impuestos']['valor_bi']) ?
                                '<i class="fa fa-asterisk"></i>' :
                                'Q ' . number_format($paquete['costos_impuestos']['valor_bi'], 2)
                            ?>
                        </th>
                    <?php else :?>
                        <th class="text-center">N/A</th>
                        <th class="text-center">N/A</th>
                        <th class="text-center">N/A</th>
                        <th class="text-center">N/A</th>
                        <th class="text-center">N/A</th>
                    <?php endif ?>
                    <th class="text-center" style="border-left: 1px solid #222;">Q <?= number_format($paquete['cobro_extra'], 2) ?></th>
                    <?php if ($pagoTarjeta) :?>
                        <th class="text-center"><?=
                            $paquete['servicio'] === 'Express' && empty($paquete['cobro_tarjeta']) ?
                            '<i class="fa fa-asterisk"></i>' : ('Q ' . number_format($paquete['cobro_tarjeta'], 2)) ?>
                        </th>
                    <?php endif ?>
                    <th class="text-center" style="color:rgba(0,0,0,0.4)"><?=
                        !isset($paquete['costos_chex']['libras']) || $paquete['costos_chex']['libras'] === '' ?
                            '<i class="fa fa-asterisk"></i>' : ('Q ' . number_format($paquete['costos_chex']['libras'], 2)) ?>
                    </th>
                    <th class="text-center" style="color:rgba(0,0,0,0.4)"><?=
                        $paquete['servicio'] === 'Express' ? (
                        !isset($paquete['costos_chex']['desaduanaje']) || $paquete['costos_chex']['desaduanaje'] === '' ?
                            '<i class="fa fa-asterisk"></i>' : ('Q ' . number_format($paquete['costos_chex']['desaduanaje'], 2))
                        ) : 'N/A'?>
                    </th>
                    <th class="text-center" style="color:rgba(0,0,0,0.4)"><?=
                        $paquete['servicio'] === 'Express' ? (
                        !isset($paquete['costos_chex']['seguro']) || $paquete['costos_chex']['seguro'] === '' ?
                            '<i class="fa fa-asterisk"></i>' : ('Q ' . number_format($paquete['costos_chex']['seguro'], 2))
                        ) : 'N/A'?>
                    </th>
                    <th class="text-center" title="<?= $paquete['chex_desglose'] ?? '' ?>"><?=
                        !isset($paquete['costos_chex']['total']) || $paquete['costos_chex']['total'] === '' ?
                            '<i class="fa fa-asterisk"></i>' : ('Q ' . number_format($paquete['costos_chex']['total'], 2)) ?>
                    </th>
                    <th class="text-center" style="border-left: 1px solid #222; color:rgba(0,0,0,0.4)"><?=
                        $paquete['servicio'] === 'Express' ? (
                        !isset($paquete['costos_impuestos']['dai']) || $paquete['costos_impuestos']['dai'] === '' ?
                            '<i class="fa fa-asterisk"></i>' : ('Q ' . number_format($paquete['costos_impuestos']['dai'], 2))
                        ) : 'N/A'?>
                    </th>
                    <th class="text-center" style="color:rgba(0,0,0,0.4)"><?=
                        $paquete['servicio'] === 'Express' ? (
                        !isset($paquete['costos_impuestos']['iva']) || $paquete['costos_impuestos']['iva'] === '' ?
                            '<i class="fa fa-asterisk"></i>' : ('Q ' . number_format($paquete['costos_impuestos']['iva'], 2))
                        ) : 'N/A'?>
                    </th>
                    <th class="text-center" title="<?= $paquete['impuestos_desglose'] ?? '' ?>"><?=
                        $paquete['servicio'] === 'Express' ? (
                        !isset($paquete['costos_impuestos']['total']) || $paquete['costos_impuestos']['total'] === '' ?
                                '<i class="fa fa-asterisk"></i>' : ('Q ' . number_format($paquete['costos_impuestos']['total'], 2))
                        ) : 'N/A'?>
                    </th>
                    <th class="text-center" style="border-left: 1px solid #222;"><?=
                        $paquete['servicio'] === 'Express' && empty($paquete['total']) ?
                            '<i class="fa fa-asterisk"></i>' : ('Q ' . number_format($paquete['total'], 2)) ?>
                    </th>
                </tr>
            <?php endforeach ?>
        </tbody>
        <tfoot>
            <tr class="mt-2 mb-2">
                <th colspan="10"></th>
                <th class="text-center" colspan="<?= $pagoTarjeta ? 6 : 5 ?>" style="border-left: 1px solid #222;">&nbsp</th>
                <th class="text-center" colspan="3" style="border-left: 1px solid #222;">&nbsp</th>
                <th class="text-center" style="border-left: 1px solid #222;">&nbsp</th>
            </tr>
            <tr class="mt-2 mb-2 horizontal-borders">
                <th colspan="10" class="text-left" style="padding-left: 12px">Resumen:</th>
                <th colspan="<?= $pagoTarjeta ? 6 : 5 ?>" style="border-left: 1px solid #222;">&nbsp;</th>
                <th colspan="3" style="border-left: 1px solid #222;">&nbsp;</th>
                <th style="border-left: 1px solid #222;">&nbsp;</th>
            </tr>
            <tr>
                <th class="text-left" style="padding-left: 12px" colspan="2"><?= $totales['paquetes'] ?> paquetes</th>
                <th class="text-right" style="padding-right: 12px" colspan="3"><?= $totales['libras'] ?> lb</th>
                <th colspan="4"></th>
                <th class="text-center">Q <?= number_format($totales['impuestos_valor_bi'], 2) ?></th>
                <th class="text-center" style="border-left: 1px solid #222;">Q <?= number_format($totales['cobros_extras'], 2) ?></th>
                <?php if ($pagoTarjeta) :?>
                    <th class="text-center">Q <?= number_format($totales['cobro_tarjeta'], 2) ?></th>
                <?php endif ?>
                <th class="text-center" style="color:rgba(0,0,0,0.4)">Q <?= number_format($totales['chex_libras'], 2) ?></th>
                <th class="text-center" style="color:rgba(0,0,0,0.4)">Q <?= number_format($totales['chex_desaduanaje'], 2) ?></th>
                <th class="text-center" style="color:rgba(0,0,0,0.4)">Q <?= number_format($totales['chex_seguro'], 2) ?></th>
                <th class="text-center">Q <?= number_format($totales['chex'], 2) ?></th>
                <th class="text-center" style="border-left: 1px solid #222; color:rgba(0,0,0,0.4)">Q <?= number_format($totales['impuestos_dai'], 2) ?></th>
                <th class="text-center" style="color:rgba(0,0,0,0.4)">Q <?= number_format($totales['impuestos_iva'], 2) ?></th>
                <th class="text-center">Q <?= number_format($totales['impuestos'], 2) ?></th>
                <th id="th-total" data-total="<?= $totales['total'] ?>" class="text-center" style="border-left: 1px solid #222;">Q <?= number_format($totales['total'], 2) ?></th>
            </tr>
        </tfoot>
    </table>

    <?php if (!empty($invalidPaquetes)): ?>
        <div id="restringir-entrega" style="display: none;"></div>
    <?php endif ?>

<?php else :?>

<!-- TABLA ESPECIFICA PARA TARIFACIONES-->
    <table style="white-space: nowrap; font-size: 12px;" id="table-entrega-mercaderia" class="display compact" width="100%" cellspacing="0">
        <thead>
            <tr style="font-size: 10px;">
                <th></th>
                <th colspan="10" style="background-color: #ffdebe; text-align: center;"><span>Datos del Paquete</span></th>
                <th colspan="3" style="background-color: #c6ddff; text-align: center;"><span>Costos del Paquete</span></th>
            </tr>
            <tr style="font-size: 9.6px;">
                <th style="text-align: left;"><span style="color:black">No.</span></th>
                <th style="text-align: center;"><span style="color:black">Servicio</span></th>
                <th class="text-center"><span style="color:black">Póliza</span></th>
                <th class="text-center"><span style="color:black">Cliente</span></th>
                <th class="text-center"><span style="color:black">Código</span></th>
                <th style="text-align: center;"><span style="color:black">Tracking</span></th>
                <th style="text-align: center;"><span style="color:black">No. Guía</span></th>
                <th style="text-align: center;"><span style="color:black">Peso lb</span></th>
                <th style="text-align: center;"><span style="color:black">Fob</span></th>
                <th style="text-align: center;"><span style="color:black">Arancel</span></th>
                <th style="text-align: center;"><span style="color:black">CIF</span></th>
                <th style="text-align: center;"><span style="color:black">Servicios</span></th>
                <th style="text-align: center;"><span style="color:black">Impuestos</span></th>
                <th style="text-align: center;"><span style="color:black">Total</span></th>
            </tr>
            <tr style="margin-bottom: 2px; margin-top: 2px;"><th>&nbsp;</th></tr>
        </thead>
        <tbody>
        <?php foreach ($tableData['paquetes'] as $paquete): ?>
            <tr style="margin-top: 2px; margin-bottom: 2px;">
                <th style="text-align: left;"><?= $paquete['num'] ?></th>
                <th style="text-align: center;"><?= $paquete['servicio'] ?></th>
                <th style="text-align: center;"><?= empty($paquete['poliza']) ?
                        '<i class="fa fa-asterisk"></i>' : $paquete['poliza'] ?>
                </th>
                <th style="text-align: center;"><?= $paquete['clientName'] ?></th>
                <th style="text-align: center;"><?= $paquete['clientChexCode'] ?></th>
                <th style="text-align: center;"><?= $paquete['tracking'] ?></th>
                <th style="text-align: center;"><?= $paquete['guide_number'] ?></th>
                <th style="text-align: center;"><?= $paquete['libras'] ?></th>
                <th style="text-align: center;"><?= empty($paquete['precio_fob']) ?
                        '<i class="fa fa-asterisk"></i>' : ('$ ' . number_format($paquete['precio_fob'], 2)) ?>
                </th>
                <th style="text-align: center;"><?= empty($paquete['arancel']) ?
                        '<i class="fa fa-asterisk"></i>' : ((100*floatval($paquete['arancel'])) . '%') ?>
                </th>
                <th class="text-center"><?=
                    $paquete['servicio'] === 'Express' ? (
                    !isset($paquete['costos_impuestos']['cif']) || $paquete['costos_impuestos']['cif'] === '' ?
                        '<i class="fa fa-asterisk"></i>' : ('Q ' . number_format($paquete['costos_impuestos']['cif'], 2))
                    ) : 'N/A'?>
                </th>
                <th style="text-align: center;" title="<?= $paquete['chex_desglose'] ?? '' ?>"><?=
                    $paquete['servicio'] === 'Express' && empty($paquete['precio_fob']) ?
                        '<i class="fa fa-asterisk"></i>' : ('Q ' . number_format($paquete['costos_chex']['total'], 2)) ?>
                </th>
                <th style="text-align: center;" title="<?= $paquete['impuestos_desglose'] ?? '' ?>"><?=
                    empty($paquete['costos_impuestos']['total']) ?
                        '<i class="fa fa-asterisk"></i>' : ('Q ' . number_format($paquete['costos_impuestos']['total'], 2))?>
                </th>
                <th style="text-align: center;"><?= empty($paquete['total']) ?
                        '<i class="fa fa-asterisk"></i>' : ('Q ' . number_format($paquete['total'], 2)) ?>
                </th>
            </tr>
        <?php endforeach ?>
        </tbody>
        <tfoot>
            <tr><th style="text-align: left;" colspan="14">&nbsp;</th></tr>
            <tr><th style="text-align: left;" colspan="14">Resumen:</th></tr>
            <tr>
                <th style="text-align: left;" colspan="2"><?= $totales['paquetes'] ?> paquetes</th>
                <th colspan="5"></th>
                <th style="text-align: center;"><?= $totales['libras'] ?></th>
                <th colspan="3"></th>
                <th style="text-align: center;">Q <?= number_format($totales['chex'], 2) ?></th>
                <th style="text-align: center;">Q <?= number_format($totales['impuestos'], 2) ?></th>
                <th id="th-total" data-total="<?= $totales['total'] ?>"
                    style="text-align: center;">Q <?= number_format($totales['total'], 2) ?>
                </th>
            </tr>
        </tfoot>
    </table>
<?php endif ?>
