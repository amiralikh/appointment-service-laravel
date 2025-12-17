<?php

use App\Jobs\SendAppointmentConfirmation;
use App\Models\Appointment;
use App\Models\HealthProfessional;
use App\Models\Service;
use Illuminate\Support\Facades\Queue;

describe('AppointmentController', function () {
    beforeEach(function () {
        Queue::fake();
    });

    describe('POST /api/v1/appointments', function () {
        it('creates a new appointment successfully', function () {
            $service = Service::factory()->create();
            $professional = HealthProfessional::factory()->create();

            $appointmentData = [
                'service_id' => $service->id,
                'health_professional_id' => $professional->id,
                'customer_email' => 'customer@example.com',
                'date' => now()->addDays(2)->format('Y-m-d H:i:s'),
                'notes' => 'First time patient',
            ];

            $response = $this->postJson('/api/v1/appointments', $appointmentData);

            $response->assertCreated()
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'service',
                        'health_professional',
                        'customer_email',
                        'scheduled_at',
                        'status',
                        'notes',
                        'created_at',
                    ],
                ])
                ->assertJsonPath('data.customer_email', 'customer@example.com')
                ->assertJsonPath('data.status', 'pending');

            $this->assertDatabaseHas('appointments', [
                'service_id' => $service->id,
                'health_professional_id' => $professional->id,
                'customer_email' => 'customer@example.com',
                'status' => 'pending',
            ]);
        });

        it('dispatches email confirmation job after creating appointment', function () {
            $service = Service::factory()->create();
            $professional = HealthProfessional::factory()->create();

            $appointmentData = [
                'service_id' => $service->id,
                'health_professional_id' => $professional->id,
                'customer_email' => 'customer@example.com',
                'date' => now()->addDays(2)->format('Y-m-d H:i:s'),
            ];

            $this->postJson('/api/v1/appointments', $appointmentData);

            Queue::assertPushed(SendAppointmentConfirmation::class);
        });

        it('validates required fields', function () {
            $response = $this->postJson('/api/v1/appointments', []);

            $response->assertUnprocessable()
                ->assertJsonValidationErrors(['service_id', 'health_professional_id', 'customer_email', 'date']);
        });

        it('validates service_id exists', function () {
            $professional = HealthProfessional::factory()->create();

            $response = $this->postJson('/api/v1/appointments', [
                'service_id' => 9999,
                'health_professional_id' => $professional->id,
                'customer_email' => 'test@example.com',
                'date' => now()->addDays(1)->format('Y-m-d H:i:s'),
            ]);

            $response->assertUnprocessable()
                ->assertJsonValidationErrors(['service_id']);
        });

        it('validates health_professional_id exists', function () {
            $service = Service::factory()->create();

            $response = $this->postJson('/api/v1/appointments', [
                'service_id' => $service->id,
                'health_professional_id' => 9999,
                'customer_email' => 'test@example.com',
                'date' => now()->addDays(1)->format('Y-m-d H:i:s'),
            ]);

            $response->assertUnprocessable()
                ->assertJsonValidationErrors(['health_professional_id']);
        });

        it('validates email format', function () {
            $service = Service::factory()->create();
            $professional = HealthProfessional::factory()->create();

            $response = $this->postJson('/api/v1/appointments', [
                'service_id' => $service->id,
                'health_professional_id' => $professional->id,
                'customer_email' => 'invalid-email',
                'date' => now()->addDays(1)->format('Y-m-d H:i:s'),
            ]);

            $response->assertUnprocessable()
                ->assertJsonValidationErrors(['customer_email']);
        });

        it('validates date must be in the future', function () {
            $service = Service::factory()->create();
            $professional = HealthProfessional::factory()->create();

            $response = $this->postJson('/api/v1/appointments', [
                'service_id' => $service->id,
                'health_professional_id' => $professional->id,
                'customer_email' => 'test@example.com',
                'date' => now()->subDay()->format('Y-m-d H:i:s'),
            ]);

            $response->assertUnprocessable()
                ->assertJsonValidationErrors(['date']);
        });

        it('validates notes max length', function () {
            $service = Service::factory()->create();
            $professional = HealthProfessional::factory()->create();

            $response = $this->postJson('/api/v1/appointments', [
                'service_id' => $service->id,
                'health_professional_id' => $professional->id,
                'customer_email' => 'test@example.com',
                'date' => now()->addDays(1)->format('Y-m-d H:i:s'),
                'notes' => str_repeat('a', 1001),
            ]);

            $response->assertUnprocessable()
                ->assertJsonValidationErrors(['notes']);
        });

        it('prevents double booking for same professional at same time', function () {
            $service = Service::factory()->create();
            $professional = HealthProfessional::factory()->create();
            $appointmentTime = now()->addDays(2)->format('Y-m-d H:i:s');

            Appointment::factory()->create([
                'health_professional_id' => $professional->id,
                'scheduled_at' => $appointmentTime,
                'status' => 'pending',
            ]);

            $response = $this->postJson('/api/v1/appointments', [
                'service_id' => $service->id,
                'health_professional_id' => $professional->id,
                'customer_email' => 'test@example.com',
                'date' => $appointmentTime,
            ]);

            $response->assertUnprocessable()
                ->assertJsonValidationErrors(['date']);
        });

        it('allows booking for different professional at same time', function () {
            $service = Service::factory()->create();
            $professional1 = HealthProfessional::factory()->create();
            $professional2 = HealthProfessional::factory()->create();
            $appointmentTime = now()->addDays(2)->format('Y-m-d H:i:s');

            Appointment::factory()->create([
                'health_professional_id' => $professional1->id,
                'scheduled_at' => $appointmentTime,
            ]);

            $response = $this->postJson('/api/v1/appointments', [
                'service_id' => $service->id,
                'health_professional_id' => $professional2->id,
                'customer_email' => 'test@example.com',
                'date' => $appointmentTime,
            ]);

            $response->assertCreated();
        });

        it('allows booking when previous appointment is cancelled', function () {
            $service = Service::factory()->create();
            $professional = HealthProfessional::factory()->create();
            $appointmentTime = now()->addDays(2)->format('Y-m-d H:i:s');

            Appointment::factory()->create([
                'health_professional_id' => $professional->id,
                'scheduled_at' => $appointmentTime,
                'status' => 'cancelled',
            ]);

            $response = $this->postJson('/api/v1/appointments', [
                'service_id' => $service->id,
                'health_professional_id' => $professional->id,
                'customer_email' => 'test@example.com',
                'date' => $appointmentTime,
            ]);

            $response->assertCreated();
        });
    });

    describe('GET /api/v1/appointments/{id}', function () {
        it('returns an appointment by id', function () {
            $appointment = Appointment::factory()->create();

            $response = $this->getJson("/api/v1/appointments/{$appointment->id}");

            $response->assertOk()
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'service',
                        'health_professional',
                        'customer_email',
                        'scheduled_at',
                        'status',
                        'notes',
                        'created_at',
                    ],
                ])
                ->assertJsonPath('data.id', $appointment->id);
        });

        it('includes service details in response', function () {
            $service = Service::factory()->create([
                'name' => 'Test Service',
                'price' => 100,
            ]);
            $appointment = Appointment::factory()->create([
                'service_id' => $service->id,
            ]);

            $response = $this->getJson("/api/v1/appointments/{$appointment->id}");

            $response->assertOk()
                ->assertJsonPath('data.service.name', 'Test Service')
                ->assertJsonPath('data.service.price', '100.00');
        });

        it('includes health professional details in response', function () {
            $professional = HealthProfessional::factory()->create([
                'name' => 'Dr. Test',
                'specialization' => 'Cardiologist',
            ]);
            $appointment = Appointment::factory()->create([
                'health_professional_id' => $professional->id,
            ]);

            $response = $this->getJson("/api/v1/appointments/{$appointment->id}");

            $response->assertOk()
                ->assertJsonPath('data.health_professional.name', 'Dr. Test')
                ->assertJsonPath('data.health_professional.specialization', 'Cardiologist');
        });

        it('returns 404 when appointment does not exist', function () {
            $response = $this->getJson('/api/v1/appointments/9999');

            $response->assertNotFound()
                ->assertJson([
                    'message' => 'Appointment not found',
                ]);
        });
    });
});
