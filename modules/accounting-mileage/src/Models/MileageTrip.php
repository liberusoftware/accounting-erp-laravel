<?php

declare(strict_types=1);

namespace Liberu\Accounting\Mileage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Liberu\Accounting\Mileage\Enums\TripStatus;

/**
 * @property TripStatus $status
 * @property string $source_hash
 * @property string $currency
 * @property string $business_purpose
 * @property string $distance_unit
 * @property string $trip_ref
 * @property string $employee_ref
 * @property string $region
 * @property string $source
 * @property string $reimbursement_amount
 * @property Carbon|null $trip_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
final class MileageTrip extends Model
{
    protected $table = 'accounting_mileage_trips';

    protected $fillable = ['team_id', 'trip_ref', 'employee_ref', 'vehicle_id', 'rate_id', 'policy_id', 'project_ref', 'origin', 'destination', 'trip_date', 'distance', 'distance_unit', 'business_purpose', 'region', 'currency', 'reimbursement_amount', 'status', 'source', 'source_hash', 'submitted_at', 'approved_at', 'reimbursed_at', 'metadata'];

    protected $casts = ['trip_date' => 'date', 'distance' => 'decimal:2', 'reimbursement_amount' => 'decimal:2', 'status' => TripStatus::class, 'submitted_at' => 'datetime', 'approved_at' => 'datetime', 'reimbursed_at' => 'datetime', 'metadata' => 'array'];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function rate(): BelongsTo
    {
        return $this->belongsTo(MileageRate::class, 'rate_id');
    }

    public function policy(): BelongsTo
    {
        return $this->belongsTo(MileagePolicy::class, 'policy_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(MileageApproval::class, 'trip_id');
    }

    public function reimbursements(): HasMany
    {
        return $this->hasMany(MileageReimbursement::class, 'trip_id');
    }
}
