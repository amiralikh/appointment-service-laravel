<?php

namespace App\Repositories\Contracts;

use App\Models\Appointment;
use Illuminate\Support\Collection;

interface AppointmentRepositoryInterface
{
    public function create(array $data): Appointment;

    public function findById(int $id): ?Appointment;

    public function getByCustomerEmail(string $email): Collection;

    public function isHealthProfessionalAvailable(
        int $healthProfessionalId,
        string $scheduledAt
    ): bool;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}
