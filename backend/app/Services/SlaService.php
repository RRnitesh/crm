<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Facades\Http;

class SlaService
{
    public function handle()
    {
        $slaTime = config('sla.high_priority_response_time');

        $now = now()->toDateTimeString();

        Ticket::where('priority', 'high')
            ->whereNull('responded_at')
            ->where('sla_breached', false)
            ->chunkById(500, function ($tickets) use ($slaTime, $now) {

                $idsToUpdate = [];

                foreach ($tickets as $ticket) {
                    $diff = $ticket->created_at->diffInMinutes($now);

                    if ($diff > $slaTime) {
                        $idsToUpdate[] = $ticket->id;
                    }
                }

                if (! empty($idsToUpdate)) {
                    // Batch update
                    Ticket::whereIn('id', $idsToUpdate)->update(['sla_breached' => true]);

                    Http::post('http://localhost:5000/api/log-breached-tickets', [
                            'ticketIds' => $idsToUpdate
                    ]);
                }
            });
    }


}
