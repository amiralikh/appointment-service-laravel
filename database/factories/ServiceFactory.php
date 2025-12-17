<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'General Consultation',
                'Physical Therapy',
                'Dental Checkup',
                'Physiotherapy Session',
                'Mental Health Counseling',
                'Nutrition Consultation',
                'Cardiology Checkup',
            ]),
            'description' => fake()->sentence(12),
            'duration_minutes' => fake()->randomElement([30, 45, 60, 90]),
            'price' => fake()->randomFloat(2, 50, 300),
        ];
    }
}
