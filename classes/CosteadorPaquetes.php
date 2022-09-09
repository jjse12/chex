<?php

require_once("../db/utils.php");
require_once("../db/server_db_vars.php");

class CosteadorPaquetes {

    const DEFAULT_TARIFA_ESTANDAR = 60;
    const DEFAULT_TARIFA_EXPRESS = 28;
    const DEFAULT_DESADUANAJE = 28;
    const DEFAULT_SEGURO = 0.02;

    private $paquetes;
    private $pagoTarjeta;
    private $recargoTarjeta;
    private $isTarifacion;
    private $isNotificacion;

    public function __construct(array $paquetes)
    {
        $this->paquetes = $paquetes;
        $this->pagoTarjeta = false;
    }

    public function setPagoTarjeta(bool $pagoTarjeta) {
        $this->pagoTarjeta = $pagoTarjeta;
    }

    public function setRecargoTarjeta(string $tipoTarjeta = null) {
        switch ($tipoTarjeta){
            case 'T.C. Visa':
                $this->recargoTarjeta = 0.05;
                break;
            case 'T.C. Credomatic':
                $this->recargoTarjeta = 0.07;
                break;
            case 'T.C. Hugo Link':
                $this->recargoTarjeta = 0.03;
                break;
            default: $this->recargoTarjeta = 0.7;
        }
    }

    public function setIsTarifacion(bool $isTarifacion) {
        $this->isTarifacion = $isTarifacion;
    }

    public function setIsNotificacion(bool $isNotificacion) {
        $this->isNotificacion = $isNotificacion;
    }

    private function getAllCoeficientesCotizaciones(){
        $query = "SELECT tarifa, desaduanaje, iva, seguro, cambio_dolar, fecha_desactivacion
            FROM cotizador_express_coeficientes
            ORDER BY fecha_desactivacion";
        $serverConn = new mysqli(SERVER_DB_HOST, SERVER_DB_USER, SERVER_DB_PASS, SERVER_DB_NAME);
        $res = $serverConn->query($query);
        if (!empty($res) && $res->num_rows > 0) {
            while($row = $res->fetch_assoc()){
                $coeficientes[] = $row;
            }
            return $coeficientes;
        }
        else {
            $error = !empty($serverConn->error) ?
                " El servidor indicó el siguitente error: $serverConn->error." :
                ($res->num_rows === 0 ?
                    ' Parece que no hay ninguna configuración de coeficientes, verifica en la base de datos' : '');
            $errorMsg = 'No se pudieron obtener los coeficientes para calcular ' .
                "los costos de los paquetes express.$error";
            throw new RuntimeException($errorMsg);
        }
    }

    private function getCoeficientesCotizacionPaquete($coeficientes, $fechaPaquete){
        $coeficientesLength = sizeof($coeficientes);
        for ($i = 1; $i < $coeficientesLength; $i++){
            $fechaDesactivacion = strtotime($coeficientes[$i]['fecha_desactivacion']);
            $diferencia = $fechaPaquete - $fechaDesactivacion;
            if ($diferencia > 0){
                return $coeficientes[$i-1];
            }
        }
        return $coeficientes[$coeficientesLength-1];
    }

    public function costear(): array
    {
        $totalLibras = 0;
        $totalCobrosExtras = 0;
        $totalCobroTarjeta = 0;
        $totalChexLibras = 0;
        $totalChexDesaduanaje = 0;
        $totalChexSeguro = 0;
        $totalChex = 0;
        $totalImpuestosValorBi = 0;
        $totalImpuestosDai = 0;
        $totalImpuestosIva = 0;
        $totalImpuestos = 0;
        $total = 0;
        $invalidPaquetes = [];

        $coeficientes = $this->getAllCoeficientesCotizaciones();

        foreach ($this->paquetes as &$paquete) {
            $totalPaquete = 0;

            if ($paquete['servicio'] === 'Express') {
                if (empty($paquete['precio_fob']) || empty($paquete['arancel'])){
                    $invalidPaquetes[] = $paquete['tracking'];
                }
                else {
                    $fechaPaquete = strtotime($paquete['fecha_ingreso']);
                    $coeficientesCotPaquete = $this->getCoeficientesCotizacionPaquete($coeficientes, $fechaPaquete);
                    $iva = (float) $coeficientesCotPaquete['iva'];
                    $cambioDolar = (float) $coeficientesCotPaquete['cambio_dolar'];

                    $defaultTarifa = (float)($coeficientesCotPaquete['tarifa'] ?? self::DEFAULT_TARIFA_EXPRESS);
                    $defaultDesaduanaje = (float)($coeficientesCotPaquete['desaduanaje'] ?? self::DEFAULT_DESADUANAJE);
                    $defaultSeguro = (float)($coeficientesCotPaquete['seguro'] ?? self::DEFAULT_SEGURO);

                    $tarifa = $defaultTarifa;
                    if (!empty($paquete['tarifa_express'])){
                        $tarifa = (float) $paquete['tarifa_express'];
                    }
                    if (!empty($paquete['tarifa_express_especial'])){
                        $tarifa = (float) $paquete['tarifa_express_especial'];
                    }

                    $desaduanaje = $defaultDesaduanaje;
                    if (!empty($paquete['desaduanaje'])){
                        $desaduanaje = (float) $paquete['desaduanaje'];
                    }

                    $seguro = $defaultSeguro;
                    if (!empty($paquete['seguro'])){
                        $seguro = (float) $paquete['seguro'];
                    }

                    $fechaSubidaTarifa = strtotime('2022-05-15');
                    if ($fechaPaquete < $fechaSubidaTarifa){
                        $tarifa -= 3;
                        $desaduanaje -= 3;
                    }

                    $cotizacion = getCotizacionExpress($tarifa, $paquete['libras'], $paquete['precio_fob'],
                        $paquete['arancel'], $desaduanaje, $iva, $seguro, $cambioDolar);

                    $paquete['costos_chex'] = $cotizacion['costos_chex'];
                    $paquete['chex_desglose'] = $cotizacion['chex_desglose'];
                    $paquete['costos_impuestos'] = $cotizacion['costos_impuestos'];
                    $paquete['impuestos_desglose'] = $cotizacion['impuestos_desglose'];

                    $totalChexLibras += $cotizacion['costos_chex']['libras'];
                    $totalChexSeguro += $cotizacion['costos_chex']['seguro'];
                    $totalChexDesaduanaje += $cotizacion['costos_chex']['desaduanaje'];
                    $totalChex += $cotizacion['costos_chex']['total'];
                    $totalImpuestosValorBi += $cotizacion['costos_impuestos']['valor_bi'];
                    $totalImpuestosDai += $cotizacion['costos_impuestos']['dai'];
                    $totalImpuestosIva += $cotizacion['costos_impuestos']['iva'];
                    $totalImpuestos += $cotizacion['costos_impuestos']['total'];
                    $totalPaquete += $cotizacion['total'];

                    if ($this->pagoTarjeta) {
                        $cobroTarjeta = $totalPaquete * $this->recargoTarjeta;
                        $paquete['cobro_tarjeta'] = $cobroTarjeta;
                        $totalCobroTarjeta += $cobroTarjeta;
                        $totalPaquete += $cobroTarjeta;
                    }

                    $paquete['total'] = $totalPaquete;
                }
            }
            else if ($paquete['servicio'] === 'Estándar' || $paquete['servicio'] === 'EstÃ¡ndar') {
                $paquete['precio_fob'] = $paquete['arancel'] = $paquete['poliza'] = '';
                $paquete['costos_impuestos'] = [
                    'valor_bi' =>  '',
                    'dai' => '',
                    'iva' => '',
                    'total' => '',
                ];

                $tarifa = !empty($paquete['tarifa_estandar']) ? $paquete['tarifa_estandar'] : self::DEFAULT_TARIFA_ESTANDAR;
                $costoChex = $tarifa * $paquete['libras'];
                $paquete['costos_chex'] = [
                    'libras' => $costoChex,
                    'total' => $costoChex
                ];
                $paquete['chex_desglose'] = '- Peso: Q ' . number_format($costoChex, 2) . "\n";

                $totalChex += $costoChex;
                $totalPaquete += $costoChex;

                if ($this->pagoTarjeta) {
                    $cobroTarjeta = 8 * $paquete['libras'];
                    $paquete['cobro_tarjeta'] = $cobroTarjeta;
                    $totalCobroTarjeta += $cobroTarjeta;
                    $totalPaquete += $cobroTarjeta;
                }

                $paquete['total'] = $totalPaquete;
            }


            if (!$this->isTarifacion || $this->isNotificacion) {
                $cobroExtra = floatval($paquete['cobro_extra']);
                $totalCobrosExtras += $cobroExtra;
                $totalPaquete += $cobroExtra;
                $paquete['total'] = $totalPaquete;
                if ($this->isNotificacion && $cobroExtra > 0) {
                    $paquete['chex_desglose'] .= '- Otros: Q ' . number_format($cobroExtra, 2);
                }
            }

            $totalLibras += $paquete['libras'];
            $total += $totalPaquete;
        }

        return [
            'paquetes' => $this->paquetes,
            'invalid_paquetes' => $invalidPaquetes,
            'totales' => [
                'libras' => $totalLibras,
                'paquetes' => count($this->paquetes),
                'cobros_extras' => $totalCobrosExtras,
                'cobro_tarjeta' => $totalCobroTarjeta,
                'chex_libras' => $totalChexLibras,
                'chex_desaduanaje' => $totalChexDesaduanaje,
                'chex_seguro' => $totalChexSeguro,
                'chex' => $totalChex,
                'impuestos_valor_bi' => $totalImpuestosValorBi,
                'impuestos_dai' => $totalImpuestosDai,
                'impuestos_iva' => $totalImpuestosIva,
                'impuestos' => $totalImpuestos,
                'total' => $total,
            ],
        ];
    }
}