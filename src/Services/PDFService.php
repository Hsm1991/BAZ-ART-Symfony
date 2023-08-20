<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class PDFService
{

    private $dompdf;

    public function __construct(){
        $this->dompdf = new Dompdf();
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont','Garamond');
        $this->dompdf->setPaper('A3','portrait');
        $this->dompdf->setOptions($pdfOptions);
    }

    public function showPdf($html){
        $this->dompdf->loadHtml($html);
        $this->dompdf->render();
        $this->dompdf->stream("facture.pdf",['Attachement'=> false]);
    }

}