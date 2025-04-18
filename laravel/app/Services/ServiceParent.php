<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

abstract class ServiceParent
{
    protected mixed $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model::all();
    }

    public function find($id)
    {
        return $this->model::find($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->model::create($data);
        });
    }

    public function update($id, array $data)
    {
        $record = $this->find($id);
        if (!$record) {
            return null;
        }

        return DB::transaction(function () use ($record, $data) {
            $record->update($data);
            return $record;
        });
    }

    public function delete($id)
    {
        $record = $this->find($id);
        if (!$record) {
            return null;
        }

        return DB::transaction(function () use ($record) {
            return $record->delete();
        });
    }
}
