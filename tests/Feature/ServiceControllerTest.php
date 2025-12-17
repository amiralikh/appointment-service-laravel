<?php

use App\Models\Service;

describe('ServiceController', function () {
    describe('GET /api/v1/services', function () {
        it('returns all services ordered by name', function () {
            Service::factory()->create(['name' => 'Dental Checkup']);
            Service::factory()->create(['name' => 'Physical Therapy']);
            Service::factory()->create(['name' => 'Cardiology Checkup']);

            $response = $this->getJson('/api/v1/services');

            $response->assertOk()
                ->assertJsonCount(3, 'data')
                ->assertJsonPath('data.0.name', 'Cardiology Checkup')
                ->assertJsonPath('data.1.name', 'Dental Checkup')
                ->assertJsonPath('data.2.name', 'Physical Therapy');
        });

        it('returns empty array when no services exist', function () {
            $response = $this->getJson('/api/v1/services');

            $response->assertOk()
                ->assertJsonCount(0, 'data');
        });

        it('returns correct service structure', function () {
            Service::factory()->create([
                'name' => 'Test Service',
                'description' => 'Test Description',
                'duration_minutes' => 60,
                'price' => 150.00,
            ]);

            $response = $this->getJson('/api/v1/services');

            $response->assertOk()
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'description',
                            'duration_minutes',
                            'price',
                            'formatted_price',
                            'created_at',
                        ],
                    ],
                ]);
        });
    });

    describe('GET /api/v1/services/{id}', function () {
        it('returns a single service by id', function () {
            $service = Service::factory()->create([
                'name' => 'Test Service',
                'description' => 'Test Description',
                'duration_minutes' => 45,
                'price' => 100.00,
            ]);

            $response = $this->getJson("/api/v1/services/{$service->id}");

            $response->assertOk()
                ->assertJson([
                    'data' => [
                        'id' => $service->id,
                        'name' => 'Test Service',
                        'description' => 'Test Description',
                        'duration_minutes' => 45,
                    ],
                ]);
        });

        it('returns 404 when service does not exist', function () {
            $response = $this->getJson('/api/v1/services/9999');

            $response->assertNotFound()
                ->assertJson([
                    'message' => 'Service not found',
                ]);
        });

        it('returns correct service structure for single service', function () {
            $service = Service::factory()->create();

            $response = $this->getJson("/api/v1/services/{$service->id}");

            $response->assertOk()
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'description',
                        'duration_minutes',
                        'price',
                        'formatted_price',
                        'created_at',
                    ],
                ]);
        });
    });
});
