<?php

use App\Enum\VaccineStatus;
use App\Models\User;
use App\Models\VaccineCenter;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('/test', function () {
    // dd(VaccineCenter::find(5)->users()->limit(10)->get());
    // dd(User::find(1)->status->name);
});

Route::view('dashboard', 'dashboard', [
    'VaccineStatus' => VaccineStatus::class,
])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
