<?php

namespace Database\Factories;

use App\Enum\VaccineStatus;
use App\Models\VaccineCenter;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'nid' => fake()->unique()->numberBetween(1000000000, 9999999999),
            'password' => static::$password ??= Hash::make('password'),
            'vaccine_center_id' => rand(1, VaccineCenter::count()),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function randomStatus(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => fake()->randomElement(array_map(fn ($case) => $case->value, VaccineStatus::cases())),
        ]);
    }
}
