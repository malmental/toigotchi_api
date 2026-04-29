<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('pets:decay')->everyMinute();