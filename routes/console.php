<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Prune old audit logs (keep 90 days)
Schedule::command('db:prune --model=App\\Models\\AuditLog')->daily();

// Prune expired cache/sessions
Schedule::command('session:gc')->hourly();
Schedule::command('cache:prune-stale-tags')->hourly();
