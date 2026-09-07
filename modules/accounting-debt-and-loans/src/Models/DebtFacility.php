<?php

declare(strict_types=1);

namespace Liberu\Accounting\DebtAndLoans\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\DebtAndLoans\Enums\DebtFacilityStatus;

final class DebtFacility extends Model
{
    protected $table = 'accounting_debt_facilities';

    protected $fillable = ['team_id', 'facility_ref', 'lender_ref', 'currency', 'limit_amount', 'drawn_amount', 'interest_rate', 'start_date', 'maturity_date', 'status', 'metadata'];

    protected $casts = ['limit_amount' => 'decimal:2', 'drawn_amount' => 'decimal:2', 'interest_rate' => 'decimal:8', 'start_date' => 'date', 'maturity_date' => 'date', 'status' => DebtFacilityStatus::class, 'metadata' => 'array'];

    public function movements(): HasMany
    {
        return $this->hasMany(DebtMovement::class, 'facility_id');
    }

    public function covenants(): HasMany
    {
        return $this->hasMany(DebtCovenant::class, 'facility_id');
    }
}
