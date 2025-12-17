<?php

namespace App\Http\Resources;

use App\Models\HealthProfessional;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read HealthProfessional $resource
 */
class HealthProfessionalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'specialization' => $this->resource->specialization,
            'email' => $this->resource->email,
            'phone' => $this->resource->phone,
            'created_at' => $this->resource->created_at?->toIso8601String(),
        ];
    }
}
