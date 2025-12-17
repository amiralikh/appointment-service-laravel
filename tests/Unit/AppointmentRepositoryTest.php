<?php

use App\Models\Appointment;
use App\Models\HealthProfessional;
use App\Models\Service;
use App\Repositories\AppointmentRepository;

describe('AppointmentRepository', function () {
    beforeEach(function () {
        $this->repository = app(AppointmentRepository::class);
    });

    describe('create', function () {
        it('creates a new appointment', function () {
            $service = Service::factory()->create();
            $professional = HealthProfessional::factory()->create();

            $data = [
                'service_id' => $service->id,
                'health_professional_id' => $professional->id,
                'customer_email' => 'test@example.com',
                'scheduled_at' => now()->addDay(),
                'status' => 'pending',
                'notes' => 'Test notes',
            ];

            $appointment = $this->repository->create($data);

            expect($appointment)->toBeInstanceOf(Appointment::class)
                ->and($appointment->service_id)->toBe($service->id)
                ->and($appointment->health_professional_id)->toBe($professional->id)
                ->and($appointment->customer_email)->toBe('test@example.com');

            $this->assertDatabaseHas('appointments', [
                'id' => $appointment->id,
                'customer_email' => 'test@example.com',
            ]);
        });
    });

    describe('findById', function () {
        it('finds appointment by id with relationships', function () {
            $appointment = Appointment::factory()->create();

            $result = $this->repository->findById($appointment->id);

            expect($result)->toBeInstanceOf(Appointment::class)
                ->and($result->id)->toBe($appointment->id)
                ->and($result->relationLoaded('service'))->toBeTrue()
                ->and($result->relationLoaded('healthProfessional'))->toBeTrue();
        });

        it('returns null when appointment does not exist', function () {
            $result = $this->repository->findById(9999);

            expect($result)->toBeNull();
        });
    });

    describe('getByCustomerEmail', function () {
        it('returns all appointments for customer', function () {
            Appointment::factory()->count(3)->create(['customer_email' => 'test@example.com']);
            Appointment::factory()->create(['customer_email' => 'other@example.com']);

            $appointments = $this->repository->getByCustomerEmail('test@example.com');

            expect($appointments)->toHaveCount(3);
        });

        it('loads relationships', function () {
            Appointment::factory()->create(['customer_email' => 'test@example.com']);

            $appointments = $this->repository->getByCustomerEmail('test@example.com');

            expect($appointments->first()->relationLoaded('service'))->toBeTrue()
                ->and($appointments->first()->relationLoaded('healthProfessional'))->toBeTrue();
        });

        it('orders appointments by scheduled_at descending', function () {
            $olderAppointment = Appointment::factory()->create([
                'customer_email' => 'test@example.com',
                'scheduled_at' => now()->addDay(),
            ]);
            $newerAppointment = Appointment::factory()->create([
                'customer_email' => 'test@example.com',
                'scheduled_at' => now()->addDays(3),
            ]);

            $appointments = $this->repository->getByCustomerEmail('test@example.com');

            expect($appointments->first()->id)->toBe($newerAppointment->id)
                ->and($appointments->last()->id)->toBe($olderAppointment->id);
        });

        it('returns empty collection when no appointments found', function () {
            $appointments = $this->repository->getByCustomerEmail('nonexistent@example.com');

            expect($appointments)->toBeEmpty();
        });
    });

    describe('isHealthProfessionalAvailable', function () {
        it('returns true when professional is available', function () {
            $professional = HealthProfessional::factory()->create();
            $scheduledAt = now()->addDays(2)->toDateTimeString();

            $result = $this->repository->isHealthProfessionalAvailable(
                $professional->id,
                $scheduledAt
            );

            expect($result)->toBeTrue();
        });

        it('returns false when professional has appointment at same time', function () {
            $professional = HealthProfessional::factory()->create();
            $scheduledAt = now()->addDays(2);

            Appointment::factory()->create([
                'health_professional_id' => $professional->id,
                'scheduled_at' => $scheduledAt,
                'status' => 'pending',
            ]);

            $result = $this->repository->isHealthProfessionalAvailable(
                $professional->id,
                $scheduledAt->toDateTimeString()
            );

            expect($result)->toBeFalse();
        });

        it('returns false when professional has appointment within an hour', function () {
            $professional = HealthProfessional::factory()->create();
            $scheduledAt = now()->addDays(2);

            Appointment::factory()->create([
                'health_professional_id' => $professional->id,
                'scheduled_at' => $scheduledAt->copy()->subMinutes(30),
                'status' => 'pending',
            ]);

            $result = $this->repository->isHealthProfessionalAvailable(
                $professional->id,
                $scheduledAt->toDateTimeString()
            );

            expect($result)->toBeFalse();
        });

        it('returns true when existing appointment is cancelled', function () {
            $professional = HealthProfessional::factory()->create();
            $scheduledAt = now()->addDays(2);

            Appointment::factory()->create([
                'health_professional_id' => $professional->id,
                'scheduled_at' => $scheduledAt,
                'status' => 'cancelled',
            ]);

            $result = $this->repository->isHealthProfessionalAvailable(
                $professional->id,
                $scheduledAt->toDateTimeString()
            );

            expect($result)->toBeTrue();
        });

        it('returns true when appointment is more than an hour away', function () {
            $professional = HealthProfessional::factory()->create();
            $scheduledAt = now()->addDays(2);

            Appointment::factory()->create([
                'health_professional_id' => $professional->id,
                'scheduled_at' => $scheduledAt->copy()->subHours(2),
                'status' => 'pending',
            ]);

            $result = $this->repository->isHealthProfessionalAvailable(
                $professional->id,
                $scheduledAt->toDateTimeString()
            );

            expect($result)->toBeTrue();
        });
    });

    describe('update', function () {
        it('updates appointment data', function () {
            $appointment = Appointment::factory()->create(['status' => 'pending']);

            $result = $this->repository->update($appointment->id, ['status' => 'confirmed']);

            expect($result)->toBeTrue();
            $appointment->refresh();
            expect($appointment->status)->toBe('confirmed');
        });

        it('returns false when appointment does not exist', function () {
            $result = $this->repository->update(9999, ['status' => 'confirmed']);

            expect($result)->toBeFalse();
        });
    });

    describe('delete', function () {
        it('deletes an appointment', function () {
            $appointment = Appointment::factory()->create();

            $result = $this->repository->delete($appointment->id);

            expect($result)->toBeTrue();
            $this->assertDatabaseMissing('appointments', ['id' => $appointment->id]);
        });

        it('returns false when appointment does not exist', function () {
            $result = $this->repository->delete(9999);

            expect($result)->toBeFalse();
        });
    });
});
