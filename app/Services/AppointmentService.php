<?php

namespace App\Services;

use App\Models\Appointment;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AppointmentService
{
    public function __construct(
        protected AppointmentRepositoryInterface $appointmentRepository
    ) {}

    public function createAppointment(array $data): Appointment
    {
        return DB::transaction(function () use ($data) {
            if (!$this->appointmentRepository->isHealthProfessionalAvailable(
                $data['health_professional_id'],
                $data['date']
            )) {
                throw ValidationException::withMessages([
                    'date' => ['The selected time slot is not available for this health professional.']
                ]);
            }

            // Convert 'date' to 'scheduled_at' for database
            $appointmentData = [
                'service_id' => $data['service_id'],
                'health_professional_id' => $data['health_professional_id'],
                'customer_email' => $data['customer_email'],
                'scheduled_at' => $data['date'],
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
            ];

            // Create appointment
            $appointment = $this->appointmentRepository->create($appointmentData);

            // Load relationships for email
            $appointment->load(['service', 'healthProfessional']);

            // Dispatch email notification job
            SendAppointmentConfirmation::dispatch($appointment);

            return $appointment;
        });
    }

    /**
     * Get appointment by ID.
     */
    public function getAppointment(int $id): ?Appointment
    {
        return $this->appointmentRepository->findById($id);
    }

    /**
     * Get customer appointments.
     */
    public function getCustomerAppointments(string $email): array
    {
        return $this->appointmentRepository
            ->getByCustomerEmail($email)
            ->toArray();
    }

    /**
     * Cancel an appointment.
     */
    public function cancelAppointment(int $id): bool
    {
        return $this->appointmentRepository->update($id, [
            'status' => 'cancelled'
        ]);
    }
}
