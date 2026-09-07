<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiCurrency\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CurrencyReconciliation extends Model
{
    protected $table = 'accounting_multi_currency_reconciliations';

    protected $fillable = ['run_id', 'reference_type', 'reference_id', 'expected_gain_loss', 'actual_gain_loss', 'variance', 'status', 'external_ref', 'notes', 'metadata'];

    protected $casts = ['expected_gain_loss' => 'decimal:2', 'actual_gain_loss' => 'decimal:2', 'variance' => 'decimal:2', 'metadata' => 'array'];

    public function run(): BelongsTo
    {
        return $this->belongsTo(RevaluationRun::class, 'run_id');
    }
}
