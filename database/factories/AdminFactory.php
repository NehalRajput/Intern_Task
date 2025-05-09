<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AdminFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => '$2y$12$wNh4qNmGjnA3efkUP3a4s.LIafsmRE7.G0b4Grzo74BK4ADsGLA76', // password
            'role_id' => Role::where('name', 'admin')->first()?->id,
        ];
    }
} 