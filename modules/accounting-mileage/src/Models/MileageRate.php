<?php

declare(strict_types=1);

namespace Liberu\Accounting\Mileage\Models;

use Illuminate\Database\Eloquent\Model;

final class MileageRate extends Model
{
    protected $table = 'accounting_mileage_rates';

    protected $fillable = ['team_id', 'region', 'vehicle_type', 'currency', 'rate_per_distance', 'effective_from', 'effective_until', 'active', 'metadata'];

    protected $casts = ['rate_per_distance' => 'decimal:6', 'effective_from' => 'date', 'effective_until' => 'date', 'active' => 'boolean', 'metadata' => 'array'];
}
