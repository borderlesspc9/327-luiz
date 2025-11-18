<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexRequest;
use Illuminate\Http\Request;
use App\Utils\DefaultResponse;
use App\Exceptions\CustomException;
use App\Interface\GlobalRepositoryInterface;
use App\Models\Permission;

class PermissionController extends Controller
{
    protected $defaultResponse;
    protected $model;
    protected $globalRepository;

    public function __construct(DefaultResponse $defaultResponse, GlobalRepositoryInterface $globalRepository)
    {
        $this->globalRepository = $globalRepository;
        $this->model = new Permission();
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

            $columns = ['id', 'name', 'slug'];

            $permissions = $this->globalRepository->all($searchParams, $columns);
            return $this->defaultResponse->successWithContent('Permissions found', 201, $permissions);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
