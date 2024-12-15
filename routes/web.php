<?php

use App\Enum\VaccineStatus;
use App\Models\User;
use App\Models\VaccineCenter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('/test', function () {
    // dump(VaccineCenter::find(1)->users()->where('users.status', 1)->pluck('id'));
    // dump(VaccineCenter::find(2)->users()->where('users.status', 1)->pluck('id'));
    // dump(VaccineCenter::find(3)->users()->where('users.status', 1)->pluck('id'));
    // dump(VaccineCenter::find(4)->users()->where('users.status', 1)->pluck('id'));
    // dump(VaccineCenter::find(5)->users()->where('users.status', 1)->pluck('id'));
    // dump('Remains for vaccination', User::where('status', 1)->pluck('id'));

    // 1. Mark users as vaccinated
    // $vaccinatedUsers = User::where('status', VaccineStatus::SCHEDULED->value)->pluck('id');
    // dump('today vaccinated users ', $vaccinatedUsers);

    // 2. Schedule for the next users
    $query = "WITH selectedUsers AS (
            SELECT u.`id`, u.`status`, u.`vaccine_center_id`, u.`created_at`, vc.`daily_seat_limit`,
                ROW_NUMBER() OVER (PARTITION BY u.vaccine_center_id ORDER BY u.created_at ASC) AS `row_number`
            FROM users u
            JOIN vaccine_centers vc ON u.`vaccine_center_id` = vc.`id`
            WHERE `status` = 1
        )
        SELECT `id` FROM selectedUsers
        WHERE `row_number` <= `daily_seat_limit` ORDER BY vaccine_center_id, created_at ASC";
    $allUsers = collect(DB::select($query))->map(fn ($user) => $user->id);
    dump('Number of user will be vaccinated tomorrow 1', $allUsers);
    // 2.1 Load only specific user but N+1 query problem. where N is the number of vaccine_center
    // $userIdsToBeScheduled = VaccineCenter::select('id', 'daily_seat_limit')->get()
    //     ->flatMap(function ($center) {
    //         return User::select('id', 'status', 'vaccine_center_id', 'created_at')->where([
    //             ['status', VaccineStatus::NOT_SCHEDULED->value],
    //             ['vaccine_center_id', $center->id],
    //         ])
    //             ->oldest()
    //             ->limit($center->daily_seat_limit)
    //             ->pluck('id');
    //     });
    // dump('Number of user will be vaccinated tomorrow', $userIdsToBeScheduled); // Number of users will be vaccinated tomorrow

    // 2.1 (alternative) Eager loading but loads all the users
    // $vaccineCenters = VaccineCenter::pluck('daily_seat_limit', 'id');
    // $userToBeScheduled = User::select('id', 'status', 'vaccine_center_id', 'created_at')->where('status', VaccineStatus::NOT_SCHEDULED->value)
    //     ->whereIn('vaccine_center_id', $vaccineCenters->keys())
    //     ->oldest()
    //     ->get()
    //     ->groupBy('vaccine_center_id')
    //     ->flatMap(function ($users, $centerId) use ($vaccineCenters) {
    //         return $users->take($vaccineCenters[$centerId]);
    //     });
    // dump($userToBeScheduled);

    // 2.2 Schedule users for vaccination (update the users data)

    // 3 Send Notification to the scheduled users

    // Tasks
    // 1. Query Optimization - remove N+1 Query Problem - Done
    // 2. Implement Dashboard with filament - Done
    // 3. Write Documentation - working
    // 4. Separate Query Class

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
