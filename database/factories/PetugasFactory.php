<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PetugasFactory extends Factory
{
    public function definition(): array
    {
        return ['nama' => fake()->name(), 'email' => fake()->unique()->safeEmail(), 'password' => 'KhususTes123!'];
    }
}
