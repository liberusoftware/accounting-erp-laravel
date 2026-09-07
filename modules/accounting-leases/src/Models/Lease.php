<?php

declare(strict_types=1);

namespace Liberu\Accounting\Leases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Liberu\Accounting\Leases\Enums\LeaseStatus;

/**
 * @property LeaseStatus $status
 * @property string $lease_ref
 * @property string $currency
 * @property string $payment_amount
 * @property string $discount_rate
 * @property string $lease_liability
 * @property string $right_of_use_asset
 * @property string $accumulated_depreciation
 * @property int $useful_life_months
 * @property Carbon|null $commencement_date
 * @property Carbon|null $end_date
 */
final class Lease extends Model
{
    protected $table = 'accounting_leases';

    protected $fillable = ['team_id', 'lease_ref', 'name', 'lessor_ref', 'asset_ref', 'commencement_date', 'end_date', 'currency', 'payment_amount', 'payment_frequency', 'interest_rate', 'discount_rate', 'useful_life_months', 'status', 'right_of_use_asset', 'lease_liability', 'accumulated_depreciation', 'metadata'];

    protected $casts = ['commencement_date' => 'date', 'end_date' => 'date', 'payment_amount' => 'decimal:2', 'interest_rate' => 'decimal:8', 'discount_rate' => 'decimal:8', 'right_of_use_asset' => 'decimal:2', 'lease_liability' => 'decimal:2', 'accumulated_depreciation' => 'decimal:2', 'status' => LeaseStatus::class, 'metadata' => 'array'];

    public function payments(): HasMany
    {
        return $this->hasMany(LeasePayment::class, 'lease_id');
    }

    public function modifications(): HasMany
    {
        return $this->hasMany(LeaseModification::class, 'lease_id');
    }

    public function disclosures(): HasMany
    {
        return $this->hasMany(LeaseDisclosure::class, 'lease_id');
    }
}
