<?php

use App\Enum\VaccineStatus;
use App\Models\User;
use App\Models\VaccineCenter;
use App\Notifications\VaccineDateScheduled;
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
    dump('Remains for vaccination', User::where('status', 1)->pluck('id'));

    // 1. Mark users as vaccinated
    $vaccinatedUsers = User::where('status', VaccineStatus::SCHEDULED->value)->pluck('id');
    DB::table('users')->whereIn('id', $vaccinatedUsers)->update([
        'status' => VaccineStatus::VACCINATED->value,
    ]);
    dump('today vaccinated users ', $vaccinatedUsers);

    // 2. Schedule for the next users
    // 2.1 Load only specific user but N+1 query problem. where N is the number of vaccine_center
    $userIdsToBeScheduled = VaccineCenter::select('id', 'daily_seat_limit')->get()
        ->flatMap(function ($center) {
            return User::select('id', 'status', 'vaccine_center_id', 'created_at')->where([
                ['status', VaccineStatus::NOT_SCHEDULED->value],
                ['vaccine_center_id', $center->id],
            ])
                ->oldest()
                ->limit($center->daily_seat_limit)
                ->pluck('id');
        });
    dump('Number of user will be vaccinated tomorrow', $userIdsToBeScheduled); // Number of users will be vaccinated tomorrow

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
    DB::table('users')->whereIn('id', $userIdsToBeScheduled)->update([
        'status' => VaccineStatus::SCHEDULED->value,
        'scheduled_at' => today()->addDay(),
    ]);

    // 3 Send Notification to the scheduled users
    User::whereIn('id', $userIdsToBeScheduled)->with('vaccineCenter:id,name')->chunk(300, function ($users) {
        foreach ($users as $user) {
            Notification::send($user, new VaccineDateScheduled(
                $user->vaccineCenter->name,
                today()->addDay()->toFormattedDayDateString()
            ));
        }
    });
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
