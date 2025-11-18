<?php

namespace App\Http\Controllers;

use App\Exceptions\customException;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\StatusRequest;
use Illuminate\Http\Request;
use App\Utils\DefaultResponse;
use App\Interface\GlobalRepositoryInterface;
use App\Models\Status;

class StatusController extends Controller
{
    protected $globalRepository;
    protected $model;
    protected $defaultResponse;

    public function __construct(DefaultResponse $defaultResponse, GlobalRepositoryInterface $globalRepository)
    {
        $this->globalRepository = $globalRepository;
        $this->model = new Status();
        $this->globalRepository->setModel($this->model);
        $this->defaultResponse = $defaultResponse;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        try {
            $searchParams = $request->validated();

            $columns = ['id', 'title', 'color', 'color_text', 'slug'];

            $status = $this->globalRepository->all($searchParams, $columns);
            return $this->defaultResponse->successWithContent('Status found', 201, $status);
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StatusRequest $request)
    {
        try{
            $payload = $request->validated();
            $payload['slug'] = Status::uniqueSlug($payload['title']);

            $status = $this->globalRepository->create($payload);

            return $this->defaultResponse->successWithContent('Status criado com sucesso!', 201, $status);
        }catch (\Exception $e){
            throw new CustomException($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Status $Status)
    {
        try {
            $status = $this->globalRepository->show($Status->id);

            return $this->defaultResponse->successWithContent('Status found', 200, $status);
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StatusRequest $request, Status $Status)
    {
        try {
            $payload = $request->validated();

            $status = $this->globalRepository->update($payload, $Status->id);

            return $this->defaultResponse->successWithContent('Status atualizado com sucesso!', 200, $status);
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Status $Status)
    {
        try {
            $this->globalRepository->delete($Status->id);

            return $this->defaultResponse->isSuccess('Status removido com sucesso!', 200);
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }
}
