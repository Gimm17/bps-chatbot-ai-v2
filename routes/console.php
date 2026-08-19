<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily synchronization of newly published BPS books & indicators
Schedule::command('bps:sync-new-publications --limit=5')->dailyAt('02:00');

// Process asynchronous PDF indexing queue
Schedule::command('queue:work --stop-when-empty --max-time=50')->everyMinute();
