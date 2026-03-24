<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\InvalidOperationException;
use App\Http\Controllers\BaseController;
use App\Http\Requests\ChangeTicketStatusRequest;
use App\Http\Requests\TicketFilterRequest;
use App\Http\Requests\TicketRequest;
use App\Services\TicketService;
use Illuminate\Support\Facades\Log;

class TicketController extends BaseController
{
    protected $service;

    public function __construct(TicketService $service)
    {
        $this->service = $service;
    }

    // GET /api/tickets
    // GET /api/tickets?perpage=5&page=2
    // GET /api/tickets?status=open&priority=high&perpage=5&page=1
    public function index(TicketFilterRequest $request)
    {
        $tickets = $this->service->getPaginatedTickets($request->validated());

        return $this->paginateResponse($tickets);
    }

    public function store(TicketRequest $request)
    {
        try {
            $result = $this->service->store($request->validated()); 
            
            return $this->executionResponse($result);

        } catch (\Exception $e) {

            $this->logException($e, 'ticket store failed');

            return $this->errorResponse();
        }
    }

    public function updateStatus(ChangeTicketStatusRequest $request, $ticketId)
    {
        try {
            $result = $this->service->changeTicketStatus($ticketId, $request->validated());

            return $this->executionResponse($result);

        } catch (InvalidOperationException $e) {

            return $this->errorResponse($e->getMessage(), 400);

        } catch (\Exception $e) {

            return $this->errorResponse();
        }

    }

    public function show($ticketId)
    {
        try {
            $record = $this->service->getTicketDetailWithComments($ticketId);

            return response()->json([
                'success' => true,
                'data' => $record,
            ], 200);

        } catch (InvalidOperationException $e) {

            return $this->errorResponse($e->getMessage(), 400);

        } catch (\Exception $e) {
            $this->logException($e, 'ticket create failed');

            return $this->errorResponse();
        }
    }
}
