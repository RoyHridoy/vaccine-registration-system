<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
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
        User::factory(1000)
            ->state(
                new Sequence(fn ($sequence) => ['created_at' => now()->subMinutes($sequence->index)])
            )->create();
    }
}
