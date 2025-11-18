<?php

namespace App\Interface;

use Illuminate\Database\Eloquent\Model;

interface GlobalRepositoryInterface
{
    public function setModel(Model $model);

    public function all(array $searchParams, array $columns = ['*'], array $relationships = []);
    
    public function create(array $data);
    
    public function update(array $data, $id);
    
    public function deleteRelation($id, $relation);
    
    public function delete($id);
    
    public function show($id);

    public function allProcess(array $searchParams, array $columns = ['*'], array $relationships = []);
}