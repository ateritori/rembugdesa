<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DecisionSession;
use App\Services\SMART\SmartRankingService;

class BackfillSmartResults extends Command
{
    protected $signature = 'smart:backfill {session_id?}';
    protected $description = 'Backfill SMART results per DM';

    public function handle(SmartRankingService $service)
    {
        $query = DecisionSession::query();

        if ($this->argument('session_id')) {
            $query->where('id', $this->argument('session_id'));
        }

        $sessions = $query->get();

        foreach ($sessions as $session) {
            $this->info("Session {$session->id}");

            foreach ($session->dms as $dm) {
                $service->calculate($session, $dm, true);
                $this->line("  DM {$dm->id} ✔");
            }
        }

        $this->info('Backfill selesai.');
        return Command::SUCCESS;
    }
}
