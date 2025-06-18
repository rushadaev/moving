<?php

namespace App\Repositories\Interfaces;

interface RequestRepositoryInterface
{
    public function getAllRequests();
    public function getRequestById($id);
    public function createRequest(array $data);
    public function updateRequest($id, array $data);
    public function deleteRequest($id);
    public function getUserRequests($userId);
} 