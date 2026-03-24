<?php

namespace App\Services;

use App\Models\Comment;

class CommentService
{
    protected $ticketService;

    protected $model;

    public function __construct(TicketService $ticketService, Comment $model)
    {
        $this->ticketService = $ticketService;
        $this->model = $model;
    }

    public function checkTicketValidity($id): bool
    {
        return $this->ticketService->checkTicketValidity($id);
    }

    public function store($data, $id): bool
    {
        $data['ticket_id'] = $id;

        $this->model->create($data);

        $ticket = $this->ticketService->fetchTicket($id);
        
        return $this->ticketService->markAsResponded($ticket);
    }
}
