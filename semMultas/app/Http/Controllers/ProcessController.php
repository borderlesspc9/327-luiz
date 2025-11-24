<?php

namespace App\Http\Controllers;

use App\Models\Process;
use Illuminate\Http\Request;
use App\Interface\GlobalRepositoryInterface;
use App\Http\Requests\IndexRequest;
use App\Utils\DefaultResponse;
use App\Exceptions\CustomException;
use App\Http\Requests\ProcessRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Service\PdfService;
use \GuzzleHttp\Client;

class ProcessController extends Controller
{

    protected $defaultResponse;
    protected $model;
    protected $globalRepository;
    protected $pdf;

    public function __construct(DefaultResponse $defaultResponse, GlobalRepositoryInterface $globalRepository, PdfService $pdfService)
    {
        $this->globalRepository = $globalRepository;
        $this->model = new Process();
        $this->globalRepository->setModel($this->model);
        $this->defaultResponse = $defaultResponse;
        $this->pdf = $pdfService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        try {
            $searchParams = $request->validated();
            $relationships = ['status.user', 'client', 'service', 'user', 'seller'];

            $columns = [
                'id',
                'user_id',
                'service_id',
                'seller_id',
                'client_id',
                'ait',
                'process_value',
                'payment_method',
                'infraction_code',
                'plate',
                'chassis',
                'renavam', 
                'process_number',
                'deadline_date',
                'slug'
            ];

            $processes = $this->globalRepository->allProcess($searchParams, $columns, $relationships);
            
            return $this->defaultResponse->successWithContent('Processes found', 201, $processes);
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
    public function store(ProcessRequest $request)
    {
        try{
            $payload = $request->validated();
            $payload['slug'] = Process::uniqueSlug('processo-' . $payload['process_number']);
            $payload['user_id'] = Auth::user()->id;

            $process = $this->globalRepository->create($payload);

            if ($payload['status_id']) {
                $process->status()->attach($request['status_id'], [
                    'user_id' => Auth::user()->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            //chamar função para atualizar o front em tempo real
            // $this->UpdateProcessInRealTime($payload['params'], $process);

            return $this->defaultResponse->successWithContent('Processo criado com sucesso!', 201, $process);
        }catch (\Exception $e){
            throw new CustomException($e->getMessage());
        }
    }

    public function updateStatus(Request $request, Process $process)
    {
        try {
            if ($request['status_id']) {
                $currentStatus = $process->status()->wherePivot('is_active', true)->first();
    
                if (!$currentStatus || $currentStatus->pivot->status_id != $request['status_id']) {
                    if ($currentStatus) {
                        $process->status()->updateExistingPivot($currentStatus->pivot->status_id, ['is_active' => false]);
                    }
    
                    $process->status()->attach($request['status_id'], [
                        'user_id' => Auth::user()->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                        
                    // if(!$request->updateDeadLine){
                    //     $this->refresh($request, $process);
                    // }else{
                    // }
                    if($request->updateDeadLine == true && !empty($request->deadline_date)){
                        $process->deadline_date = $request->deadline_date;
                        $process->update();

                        //chamar função para atualizar o front em tempo real
                        // $this->UpdateProcessInRealTime($request['params'], $process);
                    }
                }
                return $this->defaultResponse->successWithContent('Status do processo atualizado com sucesso!', 201, $process);
            }
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Process $process)
    {
        try {
            $process = $this->globalRepository->show($process->id);

            return $this->defaultResponse->successWithContent('Process found', 200, $process);
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Process $process)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProcessRequest $request, Process $process)
    {
        try {
            $payload = $request->validated();
    
            $process = $this->globalRepository->update($payload, $process->id);
    
            if (isset($payload['status_id'])) {
                $currentStatus = $process->status()->wherePivot('is_active', true)->first();
    
                if (!$currentStatus || $currentStatus->pivot->status_id != $payload['status_id']) {
                    if ($currentStatus) {
                        $process->status()->updateExistingPivot($currentStatus->pivot->status_id, ['is_active' => false]);
                    }
    
                    $process->status()->attach($request['status_id'], ['user_id' => Auth::user()->id]);
                }
            }

            //chamar função para atualizar o front em tempo real
            // $this->UpdateProcessInRealTime($payload['params'], $process);
    
            return $this->defaultResponse->successWithContent('Processo atualizado com sucesso!', 200, $process);
    
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }

    public function refresh(Request $request, Process $process)
    {
        try {
            $currentDeadline = $process->deadline_date;
            $newDeadline = Carbon::parse($currentDeadline)->addDays(15);
            $process->deadline_date = $newDeadline;

            $process->update();

            //chamar função para atualizar o front em tempo real
            // $this->UpdateProcessInRealTime($request['params'], $process);

            return $this->defaultResponse->successWithContent('Data atualizada com sucesso', 200, $process);

        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Process $process)
    {
        try {
            $process->status()->detach();

            $this->globalRepository->delete($process->id);

            //chamar função para atualizar o front em tempo real
            // $this->UpdateProcessInRealTime($request->toArray(), $process);

            return $this->defaultResponse->isSuccess('Processo removido com sucesso!', 200);
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }

    public function pdfGenerate(Process $process)
    {
        try{
            $process->load('client', 'service');
            
            $pdfContent = $this->pdf->generatePdf('pdfContract', ['content' => $process->toArray()]);
            $pdfName = $process->client->name . '_contrato.pdf';

        return response($pdfContent)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'inline; filename="' . $pdfName . '"');
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }

    public function getUniqueAgencies()
    {
        try {
            $agencies = Process::whereNotNull('agency')
                ->where('agency', '!=', '')
                ->distinct()
                ->orderBy('agency', 'asc')
                ->pluck('agency')
                ->filter()
                ->values()
                ->toArray();
            
            return $this->defaultResponse->successWithContent('Agencies found', 200, $agencies);
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }

    protected function UpdateProcessInRealTime($params, $process)
    {
        try {
            $relationships = ['status.user', 'client', 'service', 'user', 'seller'];
            $columns = [
                'id',
                'user_id',
                'service_id',
                'seller_id',
                'client_id',
                'ait',
                'process_value',
                'payment_method',
                'infraction_code',
                'plate',
                'chassis',
                'renavam', 
                'process_number',
                'deadline_date',
                'slug'
            ];

            $realTimeProcess = $this->globalRepository->allProcess($params, $columns, $relationships);

            $data = [
                'processes' => $realTimeProcess,
                'process' => $process,
                'user' => Auth::user()
            ];
            
            $client = new Client();
            $client->post(config('services.front_communication.url') . '/proccess', [
                'json' => $data
            ]);
    
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }
}
