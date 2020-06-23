<?php

require_once("../db/utils.php");
require_once("../db/server_db_vars.php");

class CosteadorPaquetes {

    const DEFAULT_TARIFA_ESTANDAR = 60;
    const DEFAULT_TARIFA_EXPRESS = 25;

    private $paquetes;
    private $pagoTarjeta;
    private $isEntrega;
    private $isNotificacion;

    public function __construct(array $paquetes)
    {
        $this->paquetes = $paquetes;
        $this->pagoTarjeta = false;
    }

    public function setPagoTarjeta(bool $pagoTarjeta) {
        $this->pagoTarjeta = $pagoTarjeta;
    }

    public function setIsEntrega(bool $isEntrega) {
        $this->isEntrega = $isEntrega;
    }

    public function setIsNotificacion(bool $isNotificacion) {
        $this->isNotificacion = $isNotificacion;
    }

    public function costear(): array
    {
        $totalLibras = 0;
        $totalCobrosExtras = 0;
        $totalCobroTarjeta = 0;
        $totalChex = 0;
        $totalImpuestos = 0;
        $total = 0;
        $invalidPaquetes = [];
        $coeficientesFetched = false;

        foreach ($this->paquetes as &$paquete) {
            $totalPaquete = 0;

            if ($paquete['servicio'] === 'Express') {
                if (empty($paquete['precio_fob']) || empty($paquete['arancel'])){
                    $invalidPaquetes[] = $paquete['tracking'];
                }
                else {
                    $tarifaFetched = self::DEFAULT_TARIFA_EXPRESS;
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
                        else {
                            $error = !empty($serverConn->error) ?
                                " El servidor indicó el siguitente error: $serverConn->error." :
                                    ($res->num_rows === 0 ?
                                ' Parece que no hay ninguna configuración de coeficientes habilitada, verifica en la base de datos' : '');
                            $errorMsg = 'No se pudieron obtener los coeficientes para calcular ' .
                                "los costos de los paquetes express.$error";
                            throw new RuntimeException($errorMsg);
                        }
                        $coeficientesFetched = true;
                    }

                    $tarifa = !empty($paquete['tarifa_express_especial']) ? $paquete['tarifa_express_especial'] :
                        (!empty($paquete['tarifa_express']) ? $paquete['tarifa_express'] : $tarifaFetched);

                    $cotizacion = getCotizacionExpress($tarifa, $paquete['libras'], $paquete['precio_fob'],
                        $paquete['arancel'], $desaduanaje, $iva, $seguro, $cambioDolar);
                    $paquete['chex'] = $cotizacion['chex'];
                    $paquete['chex_info'] = $cotizacion['chex_info'];
                    $paquete['impuestos'] = $cotizacion['impuestos'];
                    $paquete['impuestos_info'] = $cotizacion['impuestos_info'];

                    $totalChex += $cotizacion['chex'];
                    $totalImpuestos += $cotizacion['impuestos'];
                    $totalPaquete += $cotizacion['total'];

                    if ($this->pagoTarjeta) {
                        $cobroTarjeta = $totalPaquete * 0.07;
                        $paquete['cobro_tarjeta'] = $cobroTarjeta;
                        $totalCobroTarjeta += $cobroTarjeta;
                        $totalPaquete += $cobroTarjeta;
                    }

                    $paquete['total'] = $totalPaquete;
                }
            }
            else if ($paquete['servicio'] === 'Estándar' || $paquete['servicio'] === 'EstÃ¡ndar') {
                $paquete['precio_fob'] = $paquete['arancel'] = $paquete['impuestos'] = '';

                $tarifa = !empty($paquete['tarifa_estandar']) ? $paquete['tarifa_estandar'] : self::DEFAULT_TARIFA_ESTANDAR;
                $costoChex = $tarifa * $paquete['libras'];
                $paquete['chex'] = $costoChex;
                $paquete['chex_info'] = '- Peso: Q ' . number_format($costoChex, 2) . "\n";

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


            if ($this->isEntrega || $this->isNotificacion) {
                $cobroExtra = floatval($paquete['cobro_extra']);
                $totalCobrosExtras += $cobroExtra;
                $totalPaquete += $cobroExtra;
                $paquete['total'] = $totalPaquete;
                if ($this->isNotificacion && $cobroExtra > 0) {
                    $paquete['chex_info'] .= '- Otros: Q ' . number_format($cobroExtra, 2);
                }
            }

            $totalLibras += $paquete['libras'];
            $total += $totalPaquete;
        }

        return [
            'paquetes' => $this->paquetes,
            'invalid_paquetes' => $invalidPaquetes,
            'totales' => [
                'chex' => $totalChex,
                'cobros_extras' => $totalCobrosExtras,
                'cobro_tarjeta' => $totalCobroTarjeta,
                'impuestos' => $totalImpuestos,
                'libras' => $totalLibras,
                'paquetes' => count($this->paquetes),
                'total' => $total,
            ],
        ];
    }
}