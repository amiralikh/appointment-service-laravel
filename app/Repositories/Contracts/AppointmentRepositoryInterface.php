<?php

namespace App\Repositories\Contracts;

use App\Models\Appointment;
use Illuminate\Support\Collection;

interface AppointmentRepositoryInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Appointment;

    public function findById(int $id): ?Appointment;

    /**
     * @return Collection<int, Appointment>
     */
    public function getByCustomerEmail(string $email): Collection;

    public function isHealthProfessionalAvailable(
        int $healthProfessionalId,
        string $scheduledAt
    ): bool;

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}
