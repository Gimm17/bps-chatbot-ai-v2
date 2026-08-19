<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class BpsClearCacheCommand extends Command
{
    protected $signature = 'bps:clear-cache';

    protected $description = 'Clear cached BPS API responses';

    public function handle(): int
    {
        $this->info('Clearing application cache store...');
        Cache::flush();
        $this->info('✓ Cache cleared successfully.');

        return Command::SUCCESS;
    }
}
