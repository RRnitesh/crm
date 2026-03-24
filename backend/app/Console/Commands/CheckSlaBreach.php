<?php

namespace App\Console\Commands;

use App\Services\SlaService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

#[Signature('app:check-sla-breach')]
#[Description('Command description')]
class CheckSlaBreach extends Command
{
    protected $slaService;

    public function __construct(SlaService $slaService) {
         parent::__construct();

        $this->slaService = $slaService;
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::channel('api')->info('SLA breached checking');

        $this->slaService->handle();
    }
}
