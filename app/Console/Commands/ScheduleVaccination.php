<?php

namespace App\Console\Commands;

use App\Enum\VaccineStatus;
use App\Models\User;
use App\Models\VaccineCenter;
use App\Notifications\VaccineDateScheduled;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ScheduleVaccination extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:schedule-vaccination';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule Vaccination Date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. Mark already vaccinated users
        $vaccinatedUsers = User::where('status', VaccineStatus::SCHEDULED->value)->pluck('id');
        DB::table('users')->whereIn('id', $vaccinatedUsers)->update([
            'status' => VaccineStatus::VACCINATED->value,
        ]);

        // 2. Select users to provide Schedule
        // 2.1 Load only specific users (by center) but N+1 query problem. where N is the number of vaccine_center
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

        // 2.2 update the users data for schedule
        collect($userIdsToBeScheduled)->chunk(300)->each(function ($chunkedUserIds) {
            DB::table('users')->whereIn('id', $chunkedUserIds)->update([
                'status' => VaccineStatus::SCHEDULED->value,
                'scheduled_at' => today()->addDay(),
            ]);
        });

        // 3. Send Notification to the selected users
        User::whereIn('id', $userIdsToBeScheduled)
            ->with('vaccineCenter:id,name')
            ->chunk(300, function ($users) {
                foreach ($users as $user) {
                    Notification::send($user, new VaccineDateScheduled(
                        $user->vaccineCenter->name,
                        today()->addDay()->toFormattedDayDateString()
                    ));
                }
            });
    }
}
