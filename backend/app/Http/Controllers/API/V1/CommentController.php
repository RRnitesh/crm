<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\BaseController;
use App\Http\Requests\CommentRequest;
use App\Services\CommentService;
use Illuminate\Support\Facades\Log;

class CommentController extends BaseController
{
    protected $service;

    public function __construct(CommentService $service)
    {
        $this->service = $service;
    }

    public function store(CommentRequest $request, $ticketId)
    {
        if (! $this->service->checkTicketValidity($ticketId)) {
            return $this->errorResponse('Ticket not found');
        }

        try {
            $result = $this->service->store($request->validated(), $ticketId);

            return $this->executionResponse($result);

        } catch (\Exception $e) {
            $this->logException($e, 'comment creation failed');

            return $this->errorResponse();
        }
    }
}
