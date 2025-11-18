<?php

use App\Http\Controllers\ExportController;
use App\Http\Controllers\PDFController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/export/all', [ExportController::class, 'all']);

// Route::get('/pdf', [PDFController::class, 'generate']);

Route::get('pdf', function(){
    $client = new \App\Models\Client();
    //find client by id
    $client = $client->find(1);
    return view('pdfProcuration', ['content' => $client->toArray()]);
});

Route::get('/proxy-pdf', [PDFController::class, 'proxyPDF']);