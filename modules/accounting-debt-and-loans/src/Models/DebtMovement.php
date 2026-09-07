<?php

declare(strict_types=1);

namespace Liberu\Accounting\DebtAndLoans\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\DebtAndLoans\Enums\DebtMovementKind;
use Liberu\Accounting\DebtAndLoans\Enums\DebtMovementStatus;

final class DebtMovement extends Model
{
    protected $table = 'accounting_debt_movements';

    protected $fillable = ['facility_id', 'team_id', 'kind', 'principal_amount', 'interest_amount', 'fee_amount', 'movement_date', 'due_date', 'status', 'journal_ref', 'metadata'];

    protected $casts = ['kind' => DebtMovementKind::class, 'principal_amount' => 'decimal:2', 'interest_amount' => 'decimal:2', 'fee_amount' => 'decimal:2', 'movement_date' => 'date', 'due_date' => 'date', 'status' => DebtMovementStatus::class, 'metadata' => 'array'];

    public function facility(): BelongsTo
    {
        return $this->belongsTo(DebtFacility::class, 'facility_id');
    }
}
