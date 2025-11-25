<?php

namespace App\Http\Controllers;

use App\Interface\GlobalRepositoryInterface;
use App\Models\Client;
use App\Utils\DefaultResponse;
use App\Exceptions\CustomException;
use App\Http\Requests\ClientRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UploadFileRequest;
use App\Models\File;
use App\Service\FileUploadService;
use App\Service\PdfService;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    protected $globalRepository;
    protected $model;
    protected $defaultResponse;
    protected $fileUploadService;
    protected $pdf;

    public function __construct(GlobalRepositoryInterface $globalRepository, DefaultResponse $defaultResponse, FileUploadService $fileUploadService, PdfService $pdfService)
    {
        $this->globalRepository = $globalRepository;
        $this->model = new client();
        $this->globalRepository->setModel($this->model);
        $this->defaultResponse = $defaultResponse;
        $this->fileUploadService = $fileUploadService;
        $this->pdf = $pdfService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        try {
            $searchParams = $request->all();
            $relationships = ['files'];
            //escoher as colunas que deseja retornar
            $columns = ['*'];

            $clients = $this->globalRepository->all($searchParams, $columns, $relationships);

            return $this->defaultResponse->successWithContent('Clients found', 201, $clients);
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClientRequest $request)
    {
        try{
            $payload = $request->validated();
            $nameForSlug = $payload['name'] ?? 'cliente-' . uniqid();
            $payload['slug'] = Client::uniqueSlug($nameForSlug);

            $client = $this->globalRepository->create($payload);

            return $this->defaultResponse->successWithContent('Cliente cadastrado com sucesso', 201, $client);
        }catch(\Exception $e){
            throw new CustomException($e->getMessage(), 500);
        }
    }

    public function uploadFile(Client $client, UploadFileRequest $request)
    {
        try {
            $file = $request->file('file');

            $fileRecord = $this->fileUploadService->upload($file, 'clients/' . $client->slug, $request->fileName);
            
            $fileRecord->clients()->attach($client->id);

            return response()->json([
                'message' => 'Arquivo enviado com sucesso.',
                'file' => $fileRecord,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Falha ao enviar arquivo',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteFile(File $file)
    {
        try {

            $file->clients()->detach();
            $fileRecord = $this->fileUploadService->delete($file->id);

            return response()->json([
                'message' => 'Arquivo removido com sucesso.',
                'file' => $fileRecord,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Falha ao tentar remover o arquivo',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        try{
            $client->load('processes', 'processes.service', 'files');
            return $this->defaultResponse->successWithContent('Client found', 200, $client);
        }catch(\Exception $e){
            throw new CustomException($e->getMessage(), 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ClientRequest $request, Client $client)
    {
        try{
            $payload = $request->validated();

            $client = $this->globalRepository->update($payload, $client->id);

            return $this->defaultResponse->successWithContent('Cliente atualizado com sucesso', 200, $client);
        }catch(\Exception $e){
            throw new CustomException($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        try{
            $this->globalRepository->deleteRelation($client->id, 'processes');
            $this->globalRepository->delete($client->id);

            return $this->defaultResponse->isSuccess('Cliente deletado com sucesso', 200);
        }catch(\Exception $e){
            throw new CustomException($e->getMessage(), 500);
        }
    }

    public function pdfGenerate(Request $request, Client $client)
    {
        $payload = $request->validate([
            'templateName' => 'required|string'
        ]);

        try {
            switch ($payload['templateName']) {
                case 'Contrato':
                    $client->load('processes', 'processes.service');
                    $content = $client->toArray();
                    $templateName = 'pdfContract';
                    break;
                default:
                    $content = $client->toArray();
                    $templateName = 'pdfProcuration';
                    break;
            }

            // Gera o PDF
            $pdfContent = $this->pdf->generatePdf($templateName, ['content' => $content]);
            $pdfName = $templateName == 'pdfContract' ? $client->name . '_contrato.pdf' : $client->name . '_procuracao.pdf';

            // Retorna o PDF como resposta
            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="' . $pdfName . '"');

        } catch (\Exception $e) {
            throw new CustomException($e->getMessage(), 500);
        }
    }
}
