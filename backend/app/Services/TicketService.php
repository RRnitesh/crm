<?php

namespace App\Services;

use App\Enum\TicketStatusEnum;
use App\Exceptions\InvalidOperationException;
use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Support\Facades\Log;

class TicketService
{
    protected $model;

    protected $commentService;

    public function __construct(Ticket $model)
    {
        $this->model = $model;
    }

    protected array $allowedTransitions = [
        TicketStatusEnum::OPEN->value => [TicketStatusEnum::IN_PROGRESS->value, TicketStatusEnum::CLOSED->value],
        TicketStatusEnum::IN_PROGRESS->value => [TicketStatusEnum::CLOSED->value],
        TicketStatusEnum::CLOSED->value => [], // cannot change
    ];

    public function store($data): bool
    {
        $data['sla_breached'] = false;
        return (bool) $this->model->create($data);
    }

    public function checkTicketValidity(int $id): bool
    {
        return $this->model->where('id', $id)->exists();
    }

    public function getTicketDetailWithComments($ticketId)
    {
        return [
            'ticket' => $this->fetchTicket($ticketId),
            'comments' => $this->fetchTicketComments($ticketId),
        ];
    }

    public function changeTicketStatus($ticketId, array $request): bool
    {
        $newStatus = $request['status'];

        $ticket = $this->fetchTicket($ticketId);

        $current = $ticket->status;

        if (! isset($this->allowedTransitions[$current])) {
            throw new InvalidOperationException("Current status '{$current}' cannot be changed.");
        }

        if (! in_array($newStatus, $this->allowedTransitions[$current])) {
            throw new InvalidOperationException("Cannot change status from '{$current}' to '{$newStatus}'.");
        }

        $ticket->status = $newStatus;

        $ticket->save();

        return $this->markAsResponded($ticket);
    }

    public function getPaginatedTickets($filters)
    {
        $perPage = $filters['perpage'] ?? 10;
        $page = $filters['page'] ?? 1;
        $status = $filters['status'] ?? null;
        $priority = $filters['priority'] ?? null;

        $query = Ticket::query();

        if ($status) {
            $query->where('status', $status);
        }

        if ($priority) {
            $query->where('priority', $priority);
        }

        $query->orderBy('created_at', 'desc');

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function markAsResponded($ticket): bool
    {
        if ($ticket->responded_at) {
            return false;
        }

        $ticket->responded_at = now();

        $slaTime = config('sla.high_priority_response_time');

        $diff = $ticket->created_at->diffInMinutes(now());
        
        if ($ticket->priority === 'high' && $diff > $slaTime) {
            $ticket->sla_breached = true;
        }

        return $ticket->save();
    }

    public function fetchTicket(int $ticketId)
    {
        $ticket = Ticket::find($ticketId);

        if (! $ticket) {
            throw new InvalidOperationException('Ticket Not Found');
        }

        return $ticket;
    }

    private function fetchTicketComments(int $ticketId)
    {
        return Comment::where('ticket_id', $ticketId)
            ->orderBy('created_at', 'desc') // latest first
            ->select('id', 'content', 'created_at', 'created_by')
            ->get();
    }
}
