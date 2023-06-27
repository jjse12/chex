<?php

function getCotizacionExpress($tarifa, $peso, $fob, $arancel, $desaduanaje, $iva, $seguro, $cambioDolarChex, $cambioDolarImpuestos) {

    $costoLibras = round($peso*$tarifa, 2);
    $costoSeguro = round($fob*$seguro*$cambioDolarChex, 2);
    $totalChex = round($costoLibras + $desaduanaje + $costoSeguro, 2);

    $cif = $fob*$cambioDolarImpuestos + ($fob*0.022*$cambioDolarImpuestos) + ($peso*25/2.2 /* $tarifa  // Simpre usar Q25 como tarifa en esta ecuación */ );
    $dai = $arancel * $cif;
    $valorBi = $cif + $dai;
    $iva = $valorBi * $iva;
    $totalImpuestos = round($iva + $dai, 2);

    $total = $totalChex + $totalImpuestos;

    return [
        'costos_chex' => [
            'libras' => $costoLibras,
            'seguro' => $costoSeguro,
            'desaduanaje' => $desaduanaje,
            'cambio_dolar' => $cambioDolarChex,
            'total' => $totalChex
        ],
        'chex_desglose' =>
            "- Peso: Q " . number_format($costoLibras, 2) . "\n" .
            "- Desaduanaje: Q " . number_format($desaduanaje, 2) . "\n" .
            "- Seguro: Q " . number_format($costoSeguro, 2) . "\n",
        'costos_impuestos' => [
            'cambio_dolar' => $cambioDolarImpuestos,
            'valor_bi' => $valorBi,
            'dai' => $dai,
            'iva' => $iva,
            'cif' => $cif,
            'total' => $totalImpuestos
        ],
        'impuestos_desglose' =>
            "- DAI: Q " . number_format($dai,2) . "\n" .
            '- IVA: Q ' . number_format($iva, 2),
        'total' => $total
    ];
}
