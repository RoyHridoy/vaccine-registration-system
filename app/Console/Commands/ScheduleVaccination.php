<?php

namespace App\Console\Commands;

use App\Enum\VaccineStatus;
use App\Models\User;
use App\Notifications\VaccineDateScheduled;
use App\Query\UserIdsToBeScheduledQuery;
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
    protected $description = 'Update Vaccinated status and Schedule Vaccination Date';

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
        $userIdsToBeScheduled = (new UserIdsToBeScheduledQuery)->execute();

        // 3. update the users data for schedule
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
