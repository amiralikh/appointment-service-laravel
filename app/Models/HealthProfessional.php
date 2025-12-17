<?php

namespace App\Models;

use Database\Factories\HealthProfessionalFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @use HasFactory<HealthProfessionalFactory>
 */
class HealthProfessional extends Model
{
    /** @use HasFactory<HealthProfessionalFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'specialization',
        'email',
        'phone',
    ];

    /**
     * @return HasMany<Appointment, $this>
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
