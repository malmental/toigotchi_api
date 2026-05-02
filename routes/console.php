<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('pets:decay')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('pets:cleanup-memories')->daily();
