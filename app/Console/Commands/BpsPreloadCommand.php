<?php

namespace App\Console\Commands;

use App\Bps\BpsApiClient;
use Illuminate\Console\Command;

class BpsPreloadCommand extends Command
{
    protected $signature = 'bps:preload';

    protected $description = 'Pre-warm cache for BPS domains, indicators, and variables';

    public function handle(BpsApiClient $client): int
    {
        $this->info('Starting BPS WebAPI cache preloading...');

        if (empty(config('bps.key'))) {
            $this->error('BPS_WEBAPI_KEY is not configured in .env');

            return Command::FAILURE;
        }

        // 1. Domains
        $this->line('Preloading domains (all)...');
        $domResp = $client->get('domain/model/domain', ['type' => 'all']);
        if ($domResp->isOk) {
            $this->info("✓ Loaded {$domResp->total} domains.");
        } else {
            $this->warn('⚠ Domains preload: '.$domResp->errorMessage);
        }

        // 2. National Indicators
        $this->line('Preloading national indicators (domain 0000)...');
        $indResp = $client->get('list/model/indicators', ['domain' => '0000', 'page' => 1]);
        if ($indResp->isOk) {
            $this->info("✓ Loaded {$indResp->total} national indicators.");
        } else {
            $this->warn('⚠ National indicators preload: '.$indResp->errorMessage);
        }

        // 3. National Variables
        $this->line('Preloading national variables (domain 0000)...');
        $varResp = $client->get('list/model/var', ['domain' => '0000', 'page' => 1]);
        if ($varResp->isOk) {
            $this->info("✓ Loaded {$varResp->total} national variables.");
        } else {
            $this->warn('⚠ National variables preload: '.$varResp->errorMessage);
        }

        $this->info('BPS cache preloading completed successfully.');

        return Command::SUCCESS;
    }
}
