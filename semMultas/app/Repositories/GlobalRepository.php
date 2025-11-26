<?php

namespace App\Repositories;

use App\Interface\GlobalRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class GlobalRepository implements GlobalRepositoryInterface
{
    protected $model;

    public function setModel(Model $model)
    {
        $this->model = $model;
    }

    public function all(array $searchParams, array $columns = ['*'], array $relationships = [])
    {
        $whithoutPagination = $searchParams['without_pagination'] ?? false;
        
        $data = $this->model->query()
            ->with($relationships)
            ->select($columns)
            ->when($searchParams['search'] ?? null, function ($query, $search) {
                $model = $query->getModel();
                $columns = Schema::getColumnListing($model->getTable());
            
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', "%{$search}%");
                }
            })
            ->limit($searchParams['limit'] ?? null)
            ->orderBy($searchParams['order_by'] ?? 'id', $searchParams['order_direction'] ?? 'asc');

        if(isset($searchParams['filter']) && is_array($searchParams['filter'])) {
            foreach ($searchParams['filter'] as $filter => $value) {
                if (!is_null($value)) {
                    $data->where($filter, $value);
                }
            }
        }

        if (!$whithoutPagination) {
            return $data->paginate($searchParams['per_page'] ?? 10);
        }

        return $data->get();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        $update = $this->model->find($id);
        $update->update($data);
        return $update;
    }

    public function deleteRelation($id, $relation)
    {
        return $this->model->find($id)->$relation()->delete();
    }

    public function delete($id)
    {
        return $this->model->find($id)->delete();
    }

    public function show($id)
    {
        return $this->model->find($id);
    }

    public function getBySlug($relationships, $slug)
    {
        return $this->model->with($relationships)->where('slug', $slug)->first();
    }

    public function allProcess(array $searchParams, array $columns = ['*'], array $relationships = [])
    {
        $whithoutPagination = $searchParams['without_pagination'] ?? false;
        
        // Garantir que todas as chaves estrangeiras estão incluídas quando seus relacionamentos são carregados
        // Isso é necessário para que o Eloquent carregue corretamente os relacionamentos
        $columnsToSelect = $columns;
        if ($columns !== ['*']) {
            // Garantir que todas as chaves estrangeiras necessárias estão incluídas
            $foreignKeys = ['client_id', 'service_id', 'seller_id', 'user_id'];
            foreach ($foreignKeys as $foreignKey) {
                // Verificar se o relacionamento correspondente está sendo carregado
                $relationName = str_replace('_id', '', $foreignKey);
                if (in_array($relationName, $relationships) && !in_array($foreignKey, $columnsToSelect)) {
                    $columnsToSelect[] = $foreignKey;
                }
            }
        }
        
        $data = $this->model->query()
            ->with($relationships)
            ->select($columnsToSelect)
            ->when($searchParams['search'] ?? null, function ($query, $search) {
                $model = $query->getModel();
                $columns = Schema::getColumnListing($model->getTable());
            
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', "%{$search}%");
                }
            })
            ->limit($searchParams['limit'] ?? null)
            ->orderBy($searchParams['order_by'] ?? 'deadline_date', $searchParams['order_direction'] ?? 'asc');

        if(isset($searchParams['filter']) && is_array($searchParams['filter'])) {
            foreach ($searchParams['filter'] as $filter => $value) {
                if (!is_null($value)) {
                    if ($filter === 'status') {
                        $data->whereHas('status', function ($query) use ($value) {
                            $query->where('status_id', $value)
                                ->where('is_active', 1);
                        });
                    } else {
                        $data->where($filter, $value);
                    }
                }
            }
        }

        if (!$whithoutPagination) {
            return $data->paginate($searchParams['per_page'] ?? 10);
        }

        return $data->get();
    }
}