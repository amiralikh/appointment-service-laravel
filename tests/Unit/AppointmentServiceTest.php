<?php

use App\Jobs\SendAppointmentConfirmation;
use App\Models\Appointment;
use App\Models\HealthProfessional;
use App\Models\Service;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use App\Services\AppointmentService;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;

describe('AppointmentService', function () {
    beforeEach(function () {
        Queue::fake();
        $this->repository = app(AppointmentRepositoryInterface::class);
        $this->service = new AppointmentService($this->repository);
    });

    describe('createAppointment', function () {
        it('creates an appointment successfully', function () {
            $service = Service::factory()->create();
            $professional = HealthProfessional::factory()->create();

            $data = [
                'service_id' => $service->id,
                'health_professional_id' => $professional->id,
                'customer_email' => 'test@example.com',
                'date' => now()->addDay()->toDateTimeString(),
                'notes' => 'Test notes',
            ];

            $appointment = $this->service->createAppointment($data);

            expect($appointment)->toBeInstanceOf(Appointment::class)
                ->and($appointment->service_id)->toBe($service->id)
                ->and($appointment->health_professional_id)->toBe($professional->id)
                ->and($appointment->customer_email)->toBe('test@example.com')
                ->and($appointment->status)->toBe('pending');
        });

        it('dispatches email confirmation job', function () {
            $service = Service::factory()->create();
            $professional = HealthProfessional::factory()->create();

            $data = [
                'service_id' => $service->id,
                'health_professional_id' => $professional->id,
                'customer_email' => 'test@example.com',
                'date' => now()->addDay()->toDateTimeString(),
            ];

            $this->service->createAppointment($data);

            Queue::assertPushed(SendAppointmentConfirmation::class, function ($job) {
                return $job->appointment instanceof Appointment;
            });
        });

        it('throws validation exception when professional is not available', function () {
            $service = Service::factory()->create();
            $professional = HealthProfessional::factory()->create();
            $appointmentTime = now()->addDay()->toDateTimeString();

            Appointment::factory()->create([
                'health_professional_id' => $professional->id,
                'scheduled_at' => $appointmentTime,
                'status' => 'pending',
            ]);

            $data = [
                'service_id' => $service->id,
                'health_professional_id' => $professional->id,
                'customer_email' => 'test@example.com',
                'date' => $appointmentTime,
            ];

            $this->service->createAppointment($data);
        })->throws(ValidationException::class);

        it('loads service and health professional relationships', function () {
            $service = Service::factory()->create();
            $professional = HealthProfessional::factory()->create();

            $data = [
                'service_id' => $service->id,
                'health_professional_id' => $professional->id,
                'customer_email' => 'test@example.com',
                'date' => now()->addDay()->toDateTimeString(),
            ];

            $appointment = $this->service->createAppointment($data);

            expect($appointment->relationLoaded('service'))->toBeTrue()
                ->and($appointment->relationLoaded('healthProfessional'))->toBeTrue();
        });
    });

    describe('getAppointment', function () {
        it('returns appointment by id', function () {
            $appointment = Appointment::factory()->create();

            $result = $this->service->getAppointment($appointment->id);

            expect($result)->toBeInstanceOf(Appointment::class)
                ->and($result->id)->toBe($appointment->id);
        });

        it('returns null when appointment does not exist', function () {
            $result = $this->service->getAppointment(9999);

            expect($result)->toBeNull();
        });

        it('loads relationships when retrieving appointment', function () {
            $appointment = Appointment::factory()->create();

            $result = $this->service->getAppointment($appointment->id);

            expect($result->relationLoaded('service'))->toBeTrue()
                ->and($result->relationLoaded('healthProfessional'))->toBeTrue();
        });
    });

    describe('getCustomerAppointments', function () {
        it('returns all appointments for a customer email', function () {
            Appointment::factory()->count(3)->create(['customer_email' => 'test@example.com']);
            Appointment::factory()->create(['customer_email' => 'other@example.com']);

            $appointments = $this->service->getCustomerAppointments('test@example.com');

            expect($appointments)->toHaveCount(3);
        });

        it('returns empty array when customer has no appointments', function () {
            $appointments = $this->service->getCustomerAppointments('nonexistent@example.com');

            expect($appointments)->toBeEmpty();
        });

        it('returns appointments ordered by scheduled date descending', function () {
            Appointment::factory()->create([
                'customer_email' => 'test@example.com',
                'scheduled_at' => now()->addDays(3),
            ]);
            Appointment::factory()->create([
                'customer_email' => 'test@example.com',
                'scheduled_at' => now()->addDay(),
            ]);

            $appointments = $this->service->getCustomerAppointments('test@example.com');

            expect($appointments[0]['scheduled_at'] > $appointments[1]['scheduled_at'])->toBeTrue();
        });
    });

    describe('cancelAppointment', function () {
        it('cancels an appointment successfully', function () {
            $appointment = Appointment::factory()->create(['status' => 'pending']);

            $result = $this->service->cancelAppointment($appointment->id);

            expect($result)->toBeTrue();
            $appointment->refresh();
            expect($appointment->status)->toBe('cancelled');
        });

        it('returns false when appointment does not exist', function () {
            $result = $this->service->cancelAppointment(9999);

            expect($result)->toBeFalse();
        });
    });
});
