<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('app:schedule-vaccination')
    ->dailyAt('21:00')
    ->days([0, 1, 2, 3, 6])
    ->at('21:00')
    ->timezone('Asia/Dhaka');
