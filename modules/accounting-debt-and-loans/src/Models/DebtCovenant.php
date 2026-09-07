<?php

declare(strict_types=1);

namespace Liberu\Accounting\DebtAndLoans\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class DebtCovenant extends Model
{
    protected $table = 'accounting_debt_covenants';

    protected $fillable = ['facility_id', 'team_id', 'covenant_ref', 'metric', 'threshold', 'operator', 'last_value', 'status', 'metadata'];

    protected $casts = ['threshold' => 'decimal:8', 'last_value' => 'decimal:8', 'metadata' => 'array'];

    public function facility(): BelongsTo
    {
        return $this->belongsTo(DebtFacility::class, 'facility_id');
    }
}
