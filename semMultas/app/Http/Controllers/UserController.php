<?php

namespace App\Http\Controllers;

use App\Exceptions\customException;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UserRequest;
use App\Interface\GlobalRepositoryInterface;
use App\Models\User;
use App\Utils\DefaultResponse;

class UserController extends Controller
{
    protected $globalRepository;
    protected $model;
    protected $defaultResponse;

    public function __construct(DefaultResponse $defaultResponse, GlobalRepositoryInterface $globalRepository)
    {
        $this->globalRepository = $globalRepository;
        $this->model = new User();
        $this->globalRepository->setModel($this->model);
        $this->defaultResponse = $defaultResponse;
    }

    public function index(IndexRequest $request)
    {
        try {
            $relationships = ['roles'];
            $searchParams = $request->validated();

            $columns = ['id', 'name', 'email'];

            $users = $this->globalRepository->all($searchParams, $columns, $relationships);
            return $this->defaultResponse->successWithContent('User found', 200, $users);
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }

    public function store(UserRequest $request)
    {
        try {
            $payload = $request->validated();

            $payload['password'] = bcrypt($payload['password']);

            $user = $this->globalRepository->create($payload);

            if($user){
                $user->roles()->attach($payload['roles']);
            }

            return $this->defaultResponse->successWithContent('Usuário criado com sucesso!', 201, $user);

        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }

    public function show(User $user)
    {
        try {
            $user = $this->globalRepository->show($user->id);

            return $this->defaultResponse->successWithContent('User found', 200, $user);
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }

    public function update(UserRequest $request, User $user)
    {
        try {
            $payload = $request->validated();

            if (isset($payload['password']) && $payload['password'] !== '') {
                $payload['password'] = bcrypt($payload['password']);
            } else {
                unset($payload['password']);
            }

            $user = $this->globalRepository->update($payload, $user->id);

            if (isset($payload['roles'])) {
                $user->roles()->sync($payload['roles']);
            }

            return $this->defaultResponse->successWithContent('Usuário atualizado com sucesso!', 200, $user);

        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->roles()->detach();
            
            $this->globalRepository->delete($user->id);
            return $this->defaultResponse->isSuccess('Usuário removido com sucesso!', 200);

        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }
}
