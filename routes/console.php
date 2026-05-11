<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('pets:hourly-decay')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('pets:cleanup-memories')->daily();
