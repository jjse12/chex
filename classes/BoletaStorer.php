<?php
require_once("../db/db_vars.php");

/* Creates Boleta html content and then stores it on File System and DB */
class BoletaStorer
{
    const BOLETA_ID_PREFIX = 10000;

    /** @var Boleta $boleta */
    private $boleta;
    /** @var bool $mainAlreadyRendered */
    private $mainAlreadyRendered;

    /**
     * @param Boleta $boleta
     * @throws Exception
     */
    public function __construct(Boleta $boleta)
    {
        $boleta->setId($this->getNextBoletaId());
        $this->boleta = $boleta;
        $this->mainAlreadyRendered = false;
    }

    /**
     * @return int
     * @throws Exception
     */
    private function getNextBoletaId(): int
    {
        $dbName = DB_NAME;
        $mysqlConn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $queryResult = $mysqlConn->query("SELECT AUTO_INCREMENT
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = '$dbName'
        AND TABLE_NAME = 'boleta'");
        if ($queryResult->num_rows > 0) {
            $mysqlConn->close();
            return intval($queryResult->fetch_assoc()['AUTO_INCREMENT']);
        }

        $errorList = implode('***\n', array_column($mysqlConn->error_list, 'error'));
        $mysqlConn->close();
        throw new Exception('Error al intentar obtener el correlativo de boletas! ' . $errorList);
    }

    /**
     * @param string $boletaHtml
     * @param int|null $num
     * @return string
     * @throws Exception
     */
    private function createBoletaHtmlFile(string $boletaHtml, int $num = null): string
    {
        try {
            $path =  $_SERVER['DOCUMENT_ROOT'] . '/boletas/';
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $path =  $_SERVER['DOCUMENT_ROOT'] . '/chex/boletas/';
                $path = str_replace('/', '\\', $path);
            }

            $correlative = self::BOLETA_ID_PREFIX + $this->boleta->getId();
            $fileName = "boleta_" . $correlative;
            if ($num !== null)
                $fileName .= "($num)";
            $filePath = $path . $fileName . '.html';
            $file = fopen($filePath, "w");
            fwrite($file, $boletaHtml);
            fclose($file);
            return $fileName;
        } catch (Exception $e) {
            throw new Exception('Error al guardar la boleta en la carpeta de boletas: ' . $e->getMessage());
        }
    }

    /**
     * @param array $files
     * @throws Exception
     */
    private function storeBoletaInfo(array $files)
    {
        $filesArrStr = implode(',', $files);
        $mysqlConn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $result = $mysqlConn->query("INSERT INTO boleta (archivos) VALUE ('$filesArrStr')");
        if ($result !== true){
            $errorList = implode('***\n', array_column($mysqlConn->error_list, 'error'));
            $mysqlConn->close();
            throw new Exception('Error al guardar el registro de la boleta en la base de datos! ' .
                $errorList);
        }
        $mysqlConn->close();
    }

    /**
     * @throws Exception
     */
    public function store(): array
    {
        $packages = $this->boleta->getPaquetes();
        $packagesCount = count($packages);
        if ($packagesCount > 10){
            $fileNames = array();
            $counter = 1;
            do {
                $packageGroupSize = count($packages) > 10 ? 10 : count($packages);
                $packageGroup = array_slice($packages, 0, $packageGroupSize);
                $packagesOffset = ($counter-1) * 10;
                $boletaHtml = $this->getBoletaHtml($packageGroup, $packagesOffset);
                $this->mainAlreadyRendered = true;
                $fileName = $this->createBoletaHtmlFile($boletaHtml, $counter);
                array_push($fileNames, $fileName);
                $packages = array_slice($packages, 10);
                $counter++;
            } while(count($packages) > 0);
            $this->storeBoletaInfo($fileNames);
            return $fileNames;
        }

        $boletaHtml = $this->getBoletaHtml($packages);
        $boletaFileName = $this->createBoletaHtmlFile($boletaHtml);
        $this->storeBoletaInfo([$boletaFileName]);
        return [
            $boletaFileName
        ];
    }

    /**
     * @param array $packagesGroup
     * @param int $packagesOffset
     * @return string
     */
    private function getBoletaHtml(array $packagesGroup, int $packagesOffset = 0): string
    {
        return "
        <!doctype html>
        <html lang='es'>
        <head>
            <meta charset='utf-8'> " .
            $this->getBoletaHtmlStyles() . "
        </head>
        <body style='margin: 0'>
            <div class='invoice-box'>
                <table cellpadding='0' cellspacing='0'>
                    <tr class='top'>
                        <td colspan='2' style='padding-bottom: 0; padding-top: 0;'>" . $this->getBoletaHeaderHtml() . "</td>
                    </tr>          
                    <tr class='information'>
                        <td colspan='2'>" .
                            $this->getBoletaCustomerInfoSectionHtml() . "
                        </td>
                    </tr>          
                    <tr>
                        <td style='width: 50%; padding-left: 10px; padding-right: 12px'>" .
                            $this->getBoletaPackagesHtml($packagesGroup, $packagesOffset). "
                        </td>
                        <td style='width: 50%'>" .
                            $this->getTotalsAndSignSectionHtml() . "
                        </td>
                    </tr>    
                </table>
            </div>
        </body>
        </html>        
        ";
    }

    /**
     * @return string
     */
    private function getBoletaHtmlStyles(): string
    {
        return "
            <style>
                .invoice-box {
                    max-width: 760px;
                    height: 490px;
                    max-height: 490px;
                    margin: auto;
                    padding: 14px;
                    border: 3px solid #bbb;
                    font-size: 13px;
                    line-height: 13px;
                    font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
                    color: #555;
                }
        
                .invoice-box table {
                    width: 100%;
                    line-height: inherit;
                    text-align: left;
                }
        
                .invoice-box table td {
                    padding: 5px;
                    vertical-align: top;
                }
        
                .invoice-box table tr td:nth-child(2) {
                    text-align: right;
                }
        
                .invoice-box table tr.top table td {
                    padding-bottom: 4px;
                }
        
                .heading {
                    background: #eee;
                    border-bottom: 1px solid #ddd;
                    font-weight: bold;
                }
        
                td.cell {
                    border: 1px solid #eee;
                }
        
                .invoice-box table tr.total td {
                    padding-top: 10px;
                    border-bottom: 2px solid #aaa;
                }
        
                .invoice-box table tr.details td {
                    padding-bottom: 20px;
                }
        
                .invoice-box table tr.paquete td {
                    border-bottom: 1px solid #eee;
                }
        
                .invoice-box table tr.paquete.last td {
                    border-bottom: none;
                }
        
                /** RTL **/
                .rtl {
                    direction: rtl;
                    font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
                }
        
                .rtl table {
                    text-align: right;
                }
        
                .rtl table tr td:nth-child(2) {
                    text-align: left;
                }
            </style>
        ";
    }

    /**
     * @return string
     */
    private function getBoletaHeaderHtml(): string
    {
        $correlativeId = self::BOLETA_ID_PREFIX + $this->boleta->getId();
        return "
        <table>
            <tr>
                <td style='padding-top: 0; padding-bottom: 0; text-align: left; width: 33%;'>
                    <img alt='Chispudito Express' style='max-width: 128px' src='/images/logo-courier-y-carga.png'>
                </td>
                <td style='padding-top: 0; padding-bottom: 0; font-weight: bold; vertical-align: top; text-align: center; width: 33%;'>BOLETA DE ENTREGA</td>
                <td style='padding-top: 0; padding-bottom: 0; text-align: right; width: 33%;'>
                    Boleta #$correlativeId<br>" .
                    $this->boleta->getFecha() . "
                </td>
            </tr>
        </table>";
    }

    /**
     * @return string
     */
    private function getBoletaCustomerInfoSectionHtml(): string
    {
        return "
        <table>
            <tr>
                <td style='text-align: left; width: 70%;'>
                    <strong>Cliente:</strong> " . $this->boleta->getCliente() . "<br>
                    <strong>Nombre:</strong> " . $this->boleta->getReceptor() . "<br>
                    <strong>Teléfono:</strong> " . $this->boleta->getTelefono() . "<br>
                    <strong>Dirección:</strong> " . $this->boleta->getDireccion() . "
                </td>
                <td style='text-align: right;'>
                    <strong>Tipo:</strong> " . $this->boleta->getTipo() . "<br>
                    <strong>Forma de pago:</strong> " . $this->boleta->getMetodoPago() . "
                </td>
            </tr>
        </table>";
    }

    /**
     * @param array $packages
     * @param int $packagesOffset
     * @return string
     */
    private function getBoletaPackagesHtml(array $packages, int $packagesOffset): string
    {
        $allPackages = $this->boleta->getPaquetes();
        $totalPackages = sizeof($allPackages);
        $totalPounds = array_sum(array_column($allPackages, 'peso'));
        $rows = '';
        foreach ($packages as $key => $package) {
            $rowNumber = $packagesOffset + $key + 1;
            $rows .= "
                <tr class='paquete'>
                    <td style='text-align: center'>
                        $rowNumber
                    </td>
                    <td colspan='2' style='text-align: left'>
                        {$package['tracking']}
                    </td>
                    <td style='text-align: center'>
                        {$package['peso']}
                    </td>
                </tr>
            ";
        }

        return "
        <strong>&nbsp;Detalle de paquetes:</strong>
        <br>
        <table style='font-size: 12px'>
            <tr class='heading'>
                <td style='width: 10%; text-align: center;'>No.</td>
                <td colspan='2' style='width: 70%; text-align: center;'>Tracking</td>
                <td style='width: 20%; text-align: center;'>Peso</td>
            </tr>
            $rows
            <tr class='total'>
                <td colspan='2' style='text-align: left'>
                    Total de paquetes: $totalPackages
                </td>
                <td colspan='2' style='text-align: right; padding-right: 28px;'>
                    Total peso: $totalPounds
                </td>
            </tr>
        </table>
        ";
    }

    /**
     * @return string
     */
    private function getTotalsAndSignSectionHtml(): string
    {
        if ($this->mainAlreadyRendered) return "";
        $costoRutaElement = '<td colspan="2"></td>';
        if (!empty($this->boleta->getCostoRuta())){
            $costoRutaElement = "<td colspan='2'><strong>Costo ruta:</strong> " . $this->boleta->getCostoRuta() . "</td>";
        }
        return "
        <br><br>
        <table>
            <tr>
                <td colspan='2'><strong>Costo paquetes:</strong> " .
                    $this->boleta->getCostoPaquetes() . "</td>
                <td class='heading'>Total a cancelar</td>
            </tr>
            <tr>
                $costoRutaElement
                <td class='cell'>" . $this->boleta->getCostoTotal() . "</td>
            </tr>
        </table>
        <span style='display: inline-block; width: 100%; position: relative; top: 60px; padding-left: 5px; text-align: left'>
            <strong>Comentario:</strong> " . $this->boleta->getComentario() . "
        </span>
        <span style='margin-left: 15%; margin-right: 15%; width: 70%; display: inline-block; text-align: center; position: relative; top: 180px; border-top: #aaa solid 2px; padding-top: 10px'>
            Firma de recibido
        </span>
        ";
    }
}