<?php

namespace App\Repositories;

use App\Models\Appointment;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AppointmentRepository implements AppointmentRepositoryInterface
{
    public function __construct(
        protected Appointment $model
    ) {}

    public function create(array $data): Appointment
    {
        return $this->model->create($data);
    }

    public function findById(int $id): ?Appointment
    {
        return $this->model
            ->with(['service', 'healthProfessional'])
            ->find($id);
    }

    public function getByCustomerEmail(string $email): Collection
    {
        return $this->model
            ->with(['service', 'healthProfessional'])
            ->where('customer_email', $email)
            ->orderBy('scheduled_at', 'desc')
            ->get();
    }

    public function isHealthProfessionalAvailable(
        int $healthProfessionalId,
        string $scheduledAt
    ): bool
    {
        $scheduledTime = Carbon::parse($scheduledAt);

        return !$this->model
            ->where('health_professional_id', $healthProfessionalId)
            ->where('status', '!=', 'cancelled')
            ->whereBetween('scheduled_at', [
                $scheduledTime->copy()->subHour(),
                $scheduledTime->copy()->addHour(),
            ])
            ->exists();
    }

    public function update(int $id, array $data): bool
    {
        return $this->model->where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return $this->model->where('id', $id)->delete();
    }
}
