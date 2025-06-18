<?php

namespace App\Services;

use App\Models\Request as MovingRequest;
use App\Repositories\Interfaces\RequestRepositoryInterface;
use Illuminate\Support\Str;

class RequestService
{
    protected $requestRepository;

    public function __construct(RequestRepositoryInterface $requestRepository)
    {
        $this->requestRepository = $requestRepository;
    }

    public function getAllRequests()
    {
        return $this->requestRepository->getAllRequests();
    }

    public function getUserRequests($userId)
    {
        return $this->requestRepository->getUserRequests($userId);
    }

    public function createRequest(array $data)
    {
        // Generate unique request number
        $data['request_number'] = $this->generateRequestNumber();
        
        return $this->requestRepository->createRequest($data);
    }

    public function updateRequest($id, array $data)
    {
        return $this->requestRepository->updateRequest($id, $data);
    }

    public function deleteRequest($id)
    {
        return $this->requestRepository->deleteRequest($id);
    }

    protected function generateRequestNumber()
    {
        $prefix = 'REQ';
        $unique = false;
        $requestNumber = '';

        while (!$unique) {
            $requestNumber = $prefix . '-' . strtoupper(Str::random(8));
            
            // Check if request number already exists
            $exists = MovingRequest::where('request_number', $requestNumber)->exists();
            
            if (!$exists) {
                $unique = true;
            }
        }

        return $requestNumber;
    }
} 