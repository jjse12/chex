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
        'chex_info' =>
            "- Peso: Q " . number_format($costoLibras, 2) . "\n" .
            "- Desaduanaje: Q " . number_format($desaduanaje, 2) . "\n" .
            "- Seguro: Q " . number_format($costoSeguro, 2) . "\n",
        'impuestos' => $totalImpuestos,
        'impuestos_info' => '- IVA: Q ' . number_format($iva, 2) . "\n- Arancel: Q " . number_format($dai,2),
        'total' => $total
    ];
}
