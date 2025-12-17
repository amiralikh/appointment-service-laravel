<?php

namespace Database\Factories;

use App\Models\HealthProfessional;
use Illuminate\Database\Eloquent\Factories\Factory;

class HealthProfessionalFactory extends Factory
{
    protected $model = HealthProfessional::class;

    public function definition(): array
    {
        return [
            'name' => 'Dr. ' . fake()->name(),
            'specialization' => fake()->randomElement([
                'General Practitioner',
                'Cardiologist',
                'Dentist',
                'Physiotherapist',
                'Psychologist',
                'Nutritionist',
                'Pediatrician',
            ]),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
        ];
    }
}
