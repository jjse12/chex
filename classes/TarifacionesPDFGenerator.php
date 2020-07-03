<?php

require_once '../vendor/tecnickcom/tcpdf/tcpdf.php';

class TarifacionesPDFGenerator extends TCPDF {

    const COMPANY_NAME = 'Chispudito Express';

    /* @var string $tableHtml */
    private $tableHtml;

    /* @var string $displayDate*/
    private $displayDate;

    /* @var string $fileDate*/
    private $fileDate;

    /**
     * TarifacionesPDFGenerator constructor.
     * @param string $tableHtml
     * @throws Exception
     */
    public function __construct(string $tableHtml){
        parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);;

        $this->tableHtml = $tableHtml;

        $this->SetMargins(PDF_MARGIN_LEFT, 30, PDF_MARGIN_RIGHT);
        $this->SetFooterMargin(12);
        $this->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $this->SetFont('times', '', 6);

        date_default_timezone_set('America/Guatemala');
        $this->displayDate = date('d/m/Y h:i A');
        $this->fileDate = date('d-m-Y__h-i-s__A');
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
        $this->Cell('', '14', 'Ingreso de Tarifaciones','B', 0, 'R', 0);
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
    public function render() {
        $path =  $_SERVER['DOCUMENT_ROOT'] . 'tarifaciones-ingresadas/';
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $path =  $_SERVER['DOCUMENT_ROOT'] . 'chex/tarifaciones-ingresadas/';
            $path = str_replace('/', '\\', $path);
        }
        else {
            ob_start();
        }
        $this->AddPage();
        $this->Ln();
        $this->Ln();
        $this->writeHTML($this->tableHtml, true, false, false, false, '');

        $this->Output("{$path}tarifaciones__{$this->fileDate}.pdf", 'FD');
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            ob_end_flush();
        }
    }
}
