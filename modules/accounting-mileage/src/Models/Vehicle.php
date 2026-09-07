<?php

declare(strict_types=1);

namespace Liberu\Accounting\Mileage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Vehicle extends Model
{
    protected $table = 'accounting_mileage_vehicles';

    protected $fillable = ['team_id', 'owner_ref', 'registration', 'make', 'model', 'fuel_type', 'co2_g_per_km', 'active', 'metadata'];

    protected $casts = ['active' => 'boolean', 'metadata' => 'array'];

    public function trips(): HasMany
    {
        return $this->hasMany(MileageTrip::class, 'vehicle_id');
    }
}
