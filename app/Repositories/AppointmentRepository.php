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

    /**
     * @param array<string, mixed> $data
     */
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

    /**
     * @return Collection<int, Appointment>
     */
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

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool
    {
        return (bool) $this->model->where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return $this->model->where('id', $id)->delete();
    }
}
