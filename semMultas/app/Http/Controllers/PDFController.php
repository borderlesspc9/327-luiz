<?php

namespace App\Http\Controllers;

use App\Exceptions\customException;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Service\PdfService;

class PDFController extends Controller
{
    protected $pdf;

    public function __construct(PdfService $pdfService)
    {
        $this->pdf = $pdfService;
    }

    public function generate(Client $client)
    {
        try{
            $content = [
                'title' => "Contrato"
            ];

            $templateName = 'pdfProcuration';
            // $templateName = 'second-pdf';

            return $this->pdf->generatePdf($templateName, $content);

        }catch(\Exception $e){
            throw new customException($e->getMessage(), 500);
        }
    }
    
}