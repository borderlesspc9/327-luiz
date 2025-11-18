<?php

namespace App\Service;
use App\Utils\DefaultResponse;
use App\Exceptions\customException;

use PDF;

class PdfService
{
    protected $defaultResponse;

    public function __construct(DefaultResponse $defaultResponse)
    {
        $this->defaultResponse = $defaultResponse;
    }

    public function generatePdf($templateName, $content)
    {
        try{
            
            $pdf = PDF::loadView($templateName, $content);
            $pdfName = $templateName . time() . '.pdf';
            return $pdf->download($pdfName);
            // $pdfDownload = $pdf->download($pdfName);

            // return $this->defaultResponse->successWithContent("Pdf Gerado com sucesso!", 201, $pdfDownload);
        }catch(\Exception $e){
            throw new customException($e->getMessage(), 500);
        }
    }
}