<?php

namespace App\Http\Controllers;

use App\Exceptions\customException;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\RoleRequest;
use App\Interface\GlobalRepositoryInterface;
use App\Models\Role;
use App\Utils\DefaultResponse;

class RoleController extends Controller
{
    protected $globalRepository;
    protected $model;
    protected $defaultResponse;

    public function __construct(DefaultResponse $defaultResponse, GlobalRepositoryInterface $globalRepository)
    {
        $this->globalRepository = $globalRepository;
        $this->model = new Role();
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

            $relationships = ['permissions'];

            $roles = $this->globalRepository->all($searchParams, $columns, $relationships);
            return $this->defaultResponse->successWithContent('Roles found', 201, $roles);
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        try {
            $payload = $request->validated();
            $payload['slug'] = Role::uniqueSlug($payload['name']);

            $role = $this->globalRepository->create($payload);

            if ($request->has('permissions')) {
                $role->permissions()->sync($request->input('permissions'));
            }

            // Carregar as permissões associadas
            $role->load('permissions');

            return $this->defaultResponse->successWithContent('Cargo criado com sucesso!', 201, $role);

        } catch (\Exception $e) {
            return response($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $roles)
    {
        try {
            $roles->load('permissions');

            return $this->defaultResponse->successWithContent('Role found', 200, $roles);
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, Role $roles)
    {
        try {
            $payload = $request->validated();

            $role = $this->globalRepository->update($payload, $roles->id);

            if ($request->has('permissions')) {
                $role->permissions()->sync($request->input('permissions'));
            }

            $role->load('permissions');

            return $this->defaultResponse->successWithContent('Cargo atualizado com sucesso!', 200, $role);

        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $roles)
    {
        try {            
            $this->globalRepository->delete($roles->id);
            return $this->defaultResponse->isSuccess('Cargo removido com sucesso!', 200);

        } catch (\Exception $e) {
            return response($e->getMessage());
        }
    }
}
