<?php

namespace App\Repositories;

use App\Models\Request as MovingRequest;
use App\Repositories\Interfaces\RequestRepositoryInterface;

class RequestRepository implements RequestRepositoryInterface
{
    protected $model;

    public function __construct(MovingRequest $model)
    {
        $this->model = $model;
    }

    public function getAllRequests()
    {
        return $this->model->latest()->paginate(10);
    }

    public function getRequestById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function createRequest(array $data)
    {
        return $this->model->create($data);
    }

    public function updateRequest($id, array $data)
    {
        $request = $this->model->findOrFail($id);
        $request->update($data);
        return $request;
    }

    public function deleteRequest($id)
    {
        return $this->model->findOrFail($id)->delete();
    }

    public function getUserRequests($userId)
    {
        return $this->model->where('user_id', $userId)
            ->latest()
            ->paginate(10);
    }
} 