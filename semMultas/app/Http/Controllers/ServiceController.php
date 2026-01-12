<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Requests\IndexRequest;
use App\Utils\DefaultResponse;
use App\Exceptions\CustomException;
use App\Http\Requests\ServiceRequest;
use App\Interface\GlobalRepositoryInterface;

class ServiceController extends Controller
{
    protected $defaultResponse;
    protected $model;
    protected $globalRepository;

    public function __construct(DefaultResponse $defaultResponse, GlobalRepositoryInterface $globalRepository)
    {
        $this->globalRepository = $globalRepository;
        $this->model = new Service();
        $this->globalRepository->setModel($this->model);
        $this->defaultResponse = $defaultResponse;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        try{
            $searchParams = $request->validated();
            //escoher as colunas que deseja retornar
            $columns = ['id', 'name', 'process_fields', 'slug'];

            $relationships = ['processFields'];
            
            $services = $this->globalRepository->all($searchParams, $columns, $relationships);

            return $this->defaultResponse->successWithContent('Services found', 201, $services);
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
    public function store(ServiceRequest $request)
    {
        try{
            $payload = $request->validated();
            $payload['slug'] = Service::uniqueSlug($payload['name']);

            $service = $this->globalRepository->create($payload);

            $service->processFields()->attach($payload['process_fields']);

            return $this->defaultResponse->successWithContent('Serviço criado com sucesso!', 201, $service);
        }catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        try{
            
            if(!$service){
                throw new CustomException('Service not found', 404);
            }

            $service->load('processes');

            return $this->defaultResponse->successWithContent('Service found', 201, $service);
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServiceRequest $request, Service $service)
    {
        try{
            $payload = $request->validated();

            $service = $this->globalRepository->update($payload, $service->id);

            if (isset($payload['process_fields'])) {
                $service->processFields()->sync($payload['process_fields']);
            }

            return $this->defaultResponse->successWithContent('Serviço atualizado com sucesso!', 201, $service);
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        try{
            
            $this->globalRepository->deleteRelation($service->id, 'processes');

            $service->processFields()->detach();

            $this->globalRepository->delete($service->id);

            return $this->defaultResponse->isSuccess('Serviço deletado com sucesso!', 201);
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }
}
