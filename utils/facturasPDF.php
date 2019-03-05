<?php
/**
 * Created by IntelliJ IDEA.
 * User: Jenner
 * Date: 2019-02-23
 * Time: 03:00
 */


require_once '../vendor/tecnickcom/tcpdf/tcpdf.php';

class ChexFacturasPDF extends TCPDF {

    const COMPANY_NAME = "Chispudito Express";

    /* @var array $facturas */
    private $facturas;

    /* @var string $displayDate*/
    private $displayDate;

    /* @var string $fileDate*/
    private $fileDate;

    /**
     * ChexFacturasPDF constructor.
     * @param $facturas
     * @throws Exception
     */
    public function __construct($facturas){
        parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);;

        $this->facturas = $facturas;

        $this->SetMargins(PDF_MARGIN_LEFT, 20, PDF_MARGIN_RIGHT);
        $this->SetFooterMargin(12);
        $this->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $this->SetFont('helvetica', '', 10, '', true);

        /*
        $date = new \DateTime('now', new \DateTimeZone('America/Guatemala'));
        $this->displayDate = $date->format('d/m/Y H:i A');
        $this->fileDate = $date->format('Y-m-d__H:i:s');
        */
        date_default_timezone_set('America/Guatemala');
        $this->displayDate = date('d/m/Y H:i A');
        $this->fileDate = date('Y-m-d__H:i:s');
    }

    /**
     * Retrieve printable with in the document
     * @return int
     */
    public function getPrintableWidth(){
        $margins = $this->getMargins();
        $pageWidth   = $this->getPageWidth();
        return $pageWidth - $margins['left'] - $margins['right'];
    }

    /**
     * Retrieve real width size for a given percentage
     * @param integer $percentage
     * @return float
     */
    public function width($percentage){

        $printableWidth = $this->getPrintableWidth();
        return ($percentage/100) * $printableWidth;
    }

    /**
     * Override header
     */
    public function Header() {
        $path =  $_SERVER['DOCUMENT_ROOT'];
        $this->Cell('', '14', "Fecha: {$this->displayDate}", 'B',0, 'L', 0);
        $this->Image("{$path}images/logo.png", '', '', '20', '', 'png', '', '', false, 300, 'C', false, false, 0, false, false, true);
        $this->Cell('', '14', 'Reporte de Facturas','B', 0, 'R', 0);
    }

    /**
     * Override footer
     */
    public function Footer() {

        //Page number
        $this->Cell('', '',$this->getAliasNumPage().'/'.$this->getAliasNbPages(),'T', 0, 'C', 0);

        //Company
        $this->Cell('', '',self::COMPANY_NAME, 'T', 0, 'R', 0);
    }

    /**
     * @throws Exception
     */
    public function render(){

        foreach ($this->facturas as $factura){
            $this->AddPage();
            $formatedDateCreated = (new DateTime($factura['date_created']))->format('d/m/Y H:i A');
            $this->Cell(0, 0, "Fecha de Creación: {$formatedDateCreated}", '', 0, 'L', 0);
            $this->Ln();
            $this->Cell(0, 0, "Id Cliente: {$factura['clientId']}", '', 0, 'L', 0);
            $this->Ln();
            $this->Cell(0, 0, "Tracking: {$factura['tracking']}", '', 0, 'L', 0);
            $this->Ln();
            $this->Cell(0, 0, "Monto: \${$factura['amount']}", '', 0, 'L', 0);
            $this->Ln();
            $this->Cell(0, 0, "Descripción: {$factura['description']}", '', 0, 'L', 0);
            $this->Ln();
            foreach ($factura['images'] AS $value){
                $this->Ln();
                $this->Image("@".base64_decode($value['image']), '', '', '', '', explode('/', $value['image_type'])[1], '','B', false, 300, 'C', false, false, 0, false, false, true);
            }
            $this->Ln();
            $this->writeHTML('<hr>', true, false, false, false, '');
        }

        $path =  $_SERVER['DOCUMENT_ROOT'];
        $this->Output("{$path}reportesFacturas/facturas__{$this->fileDate}.pdf", 'FD');
    }
}

$facturas = $_POST['facturas'];

if (isset($facturas)){
    $pdf = new ChexFacturasPDF($facturas);
    $pdf->render();
}
else {
    header("HTTP/1.1 500 Internal Server Error");
    echo "Error en la solicitud enviada.";
}