<?php

declare(strict_types=1);

namespace Liberu\Accounting\Mileage\Models;

use Illuminate\Database\Eloquent\Model;

final class MileagePolicy extends Model
{
    protected $table = 'accounting_mileage_policies';

    protected $fillable = ['team_id', 'name', 'region', 'max_distance_per_trip', 'max_distance_per_day', 'requires_purpose', 'requires_project', 'approval_threshold', 'currency', 'active', 'metadata'];

    protected $casts = ['max_distance_per_trip' => 'decimal:2', 'max_distance_per_day' => 'decimal:2', 'approval_threshold' => 'decimal:2', 'requires_purpose' => 'boolean', 'requires_project' => 'boolean', 'active' => 'boolean', 'metadata' => 'array'];
}
