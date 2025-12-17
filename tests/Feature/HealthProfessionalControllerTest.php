<?php

use App\Models\HealthProfessional;

describe('HealthProfessionalController', function () {
    describe('GET /api/v1/health-professionals', function () {
        it('returns all health professionals ordered by name', function () {
            HealthProfessional::factory()->create(['name' => 'Dr. John Doe']);
            HealthProfessional::factory()->create(['name' => 'Dr. Alice Smith']);
            HealthProfessional::factory()->create(['name' => 'Dr. Bob Johnson']);

            $response = $this->getJson('/api/v1/health-professionals');

            $response->assertOk()
                ->assertJsonCount(3, 'data')
                ->assertJsonPath('data.0.name', 'Dr. Alice Smith')
                ->assertJsonPath('data.1.name', 'Dr. Bob Johnson')
                ->assertJsonPath('data.2.name', 'Dr. John Doe');
        });

        it('returns empty array when no health professionals exist', function () {
            $response = $this->getJson('/api/v1/health-professionals');

            $response->assertOk()
                ->assertJsonCount(0, 'data');
        });

        it('returns correct health professional structure', function () {
            HealthProfessional::factory()->create([
                'name' => 'Dr. Test',
                'specialization' => 'Cardiologist',
                'email' => 'test@example.com',
                'phone' => '123-456-7890',
            ]);

            $response = $this->getJson('/api/v1/health-professionals');

            $response->assertOk()
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'specialization',
                            'email',
                            'phone',
                            'created_at',
                        ],
                    ],
                ]);
        });

        it('includes all required fields in response', function () {
            HealthProfessional::factory()->create([
                'name' => 'Dr. Complete',
                'specialization' => 'General Practitioner',
                'email' => 'complete@example.com',
                'phone' => '555-1234',
            ]);

            $response = $this->getJson('/api/v1/health-professionals');

            $response->assertOk();
            $professional = $response->json('data.0');

            expect($professional)->toHaveKeys(['id', 'name', 'specialization', 'email', 'phone', 'created_at']);
        });
    });

    describe('GET /api/v1/health-professionals/{id}', function () {
        it('returns a single health professional by id', function () {
            $professional = HealthProfessional::factory()->create([
                'name' => 'Dr. Jane Doe',
                'specialization' => 'Pediatrician',
                'email' => 'jane@example.com',
                'phone' => '987-654-3210',
            ]);

            $response = $this->getJson("/api/v1/health-professionals/{$professional->id}");

            $response->assertOk()
                ->assertJson([
                    'data' => [
                        'id' => $professional->id,
                        'name' => 'Dr. Jane Doe',
                        'specialization' => 'Pediatrician',
                        'email' => 'jane@example.com',
                        'phone' => '987-654-3210',
                    ],
                ]);
        });

        it('returns 404 when health professional does not exist', function () {
            $response = $this->getJson('/api/v1/health-professionals/9999');

            $response->assertNotFound()
                ->assertJson([
                    'message' => 'Health professional not found',
                ]);
        });

        it('returns correct structure for single health professional', function () {
            $professional = HealthProfessional::factory()->create();

            $response = $this->getJson("/api/v1/health-professionals/{$professional->id}");

            $response->assertOk()
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'specialization',
                        'email',
                        'phone',
                        'created_at',
                    ],
                ]);
        });
    });
});
