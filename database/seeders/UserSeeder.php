<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@vaccine.com',
            'is_admin' => 1,
        ]);

        // User::factory(10)->randomStatus()->create();
        User::factory(1000)->create();
    }
}
