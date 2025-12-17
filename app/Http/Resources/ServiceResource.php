<?php

namespace App\Http\Resources;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Service $resource
 */
class ServiceResource extends JsonResource
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
            'description' => $this->resource->description,
            'duration_minutes' => $this->resource->duration_minutes,
            'price' => number_format((float)$this->resource->price, 2, '.', ''),
            'formatted_price' => '$' . number_format((float)$this->resource->price, 2),
            'created_at' => $this->resource->created_at?->toIso8601String(),
        ];
    }
}
