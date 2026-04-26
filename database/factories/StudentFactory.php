<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nis' => fake()->unique()->numerify('24####'),
            'name' => fake()->name(),
            'gender' => fake()->randomElement(['L', 'P']),
            'room' => fake()->bothify('?#'),
            'status' => 'aktif',
        ];
    }
}
