<?php

function getCotizacionExpress($tarifa, $peso, $fob, $arancel, $desaduanaje, $iva, $seguro, $cambioDolar) {
    $totalChex = round(($peso*$tarifa) + $desaduanaje + ($fob*$seguro*$cambioDolar), 2);

    $cif = $fob*$cambioDolar + ($fob*$seguro*$cambioDolar) + ($peso*$tarifa);
    $dai = $arancel * $cif;
    $iva = ($cif + $dai) * $iva;
    $totalImpuestos = round($iva + $dai, 2);

    $total = $totalChex + $totalImpuestos;

    return [
        'chex' => $totalChex,
        'impuestos' => $totalImpuestos,
        'total' => $total
    ];
}
