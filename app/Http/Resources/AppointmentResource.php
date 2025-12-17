<?php

namespace App\Http\Resources;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Appointment $resource
 */
class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $service = $this->resource->service;
        $healthProfessional = $this->resource->healthProfessional;

        return [
            'id' => $this->resource->id,
            'service' => [
                'id' => $service->id,
                'name' => $service->name,
                'duration_minutes' => $service->duration_minutes,
                'price' => $service->price,
            ],
            'health_professional' => [
                'id' => $healthProfessional->id,
                'name' => $healthProfessional->name,
                'specialization' => $healthProfessional->specialization,
            ],
            'customer_email' => $this->resource->customer_email,
            'scheduled_at' => $this->resource->scheduled_at->toIso8601String(),
            'status' => $this->resource->status,
            'notes' => $this->resource->notes,
            'created_at' => $this->resource->created_at->toIso8601String(),
        ];
    }
}
