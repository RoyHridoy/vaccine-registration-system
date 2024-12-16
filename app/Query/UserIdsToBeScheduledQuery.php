<?php

namespace App\Query;

use App\Enum\VaccineStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserIdsToBeScheduledQuery
{
    public function execute(): Collection
    {
        $query = 'WITH selectedUsers AS (
            SELECT u.`id`, u.`status`, u.`vaccine_center_id`, u.`created_at`, vc.`daily_seat_limit`,
                ROW_NUMBER() OVER (PARTITION BY u.vaccine_center_id ORDER BY u.created_at ASC) AS `row_number`
            FROM users u
            JOIN vaccine_centers vc ON u.`vaccine_center_id` = vc.`id`
            WHERE `status` = '.VaccineStatus::NOT_SCHEDULED->value.'
        )
            SELECT `id` FROM selectedUsers
            WHERE `row_number` <= `daily_seat_limit`
            ORDER BY vaccine_center_id, created_at ASC';

        return collect(DB::select($query))->map(fn ($user) => $user->id);
    }
}
