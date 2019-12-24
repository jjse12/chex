<?php

function getCotizacionExpress($tarifa, $peso, $fob, $arancel, $desaduanaje, $iva, $seguro, $cambioDolar) {

    $costoLibras = round($peso*$tarifa, 2);
    $costoSeguro = round($fob*$seguro*$cambioDolar, 2);
    $totalChex = round($costoLibras + $desaduanaje + $costoSeguro, 2);

    $cif = $fob*$cambioDolar + ($fob*$seguro*$cambioDolar) + ($peso*$tarifa);
    $dai = $arancel * $cif;
    $iva = ($cif + $dai) * $iva;
    $totalImpuestos = round($iva + $dai, 2);

    $total = $totalChex + $totalImpuestos;

    return [
        'chex' => $totalChex,
        'chexInfo' => "- Peso: Q $costoLibras\n- Desaduanaje: Q $desaduanaje\n- Seguro: Q $costoSeguro",
        'impuestos' => $totalImpuestos,
        'impuestosInfo' => '- IVA: Q ' . round($iva, 2) . "\n- Arancel: Q " . round($dai,2),
        'total' => $total
    ];
}
