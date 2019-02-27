<?php
/**
 * Created by IntelliJ IDEA.
 * User: Jenner
 * Date: 2019-02-23
 * Time: 03:00
 */

require_once 'vendor/tecnickcom/tcpdf/tcpdf.php';

$facturas = $_POST['facturas'];
if (isset($facturas)){
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetMargins(PDF_MARGIN_LEFT, 20, PDF_MARGIN_RIGHT);
    $pdf->SetFooterMargin(10);
    $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->SetFont('helvetica', '', 10, '', true);
    foreach ($facturas as $factura){
        $pdf->AddPage();
        $pdf->Cell(0, 0, "Id Cliente: {$factura['clientId']}", '', 0, 'L', 0);
        $pdf->Ln();
        $pdf->Cell(0, 0, "Tracking: {$factura['tracking']}", '', 0, 'L', 0);
        $pdf->Ln();
        $pdf->Cell(0, 0, "Monto: \${$factura['amount']}", '', 0, 'L', 0);
        $pdf->Ln();
        $pdf->Cell(0, 0, "Descripción: {$factura['description']}", '', 0, 'L', 0);
        $pdf->Ln();
        foreach ($factura['images'] AS $value){
            $pdf->Ln();
            $pdf->Image("@".base64_decode($value['image']), '', '', '', '', explode('/', $value['image_type'])[1], '','B', false, 300, 'C', false, false, 0, false, false, true);

        }
        $pdf->Ln();
        $pdf->writeHTML('<hr>', true, false, false, false, '');
    }
    date_default_timezone_set('America/Guatemala');
    $date = date('Y-m-d__H:i:s');
    $path =  $_SERVER['DOCUMENT_ROOT'];
    $pdf->Output("{$path}reportes-facturas/facturas__{$date}.pdf", 'FD');
//    $pdf->Output('facturas.pdf', 'D');
}

exit(-1);
