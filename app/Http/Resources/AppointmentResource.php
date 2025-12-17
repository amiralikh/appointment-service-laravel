<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service' => [
                'id' => $this->service->id,
                'name' => $this->service->name,
                'duration_minutes' => $this->service->duration_minutes,
                'price' => $this->service->price,
            ],
            'health_professional' => [
                'id' => $this->healthProfessional->id,
                'name' => $this->healthProfessional->name,
                'specialization' => $this->healthProfessional->specialization,
            ],
            'customer_email' => $this->customer_email,
            'scheduled_at' => $this->scheduled_at->toIso8601String(),
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
