<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiCurrency\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\MultiCurrency\Enums\GainStatus;

final class RevaluationPosition extends Model
{
    protected $table = 'accounting_multi_currency_positions';

    protected $fillable = ['run_id', 'reference_type', 'reference_id', 'currency', 'foreign_amount', 'book_rate', 'closing_rate', 'book_value', 'closing_value', 'gain_loss', 'gain_status', 'metadata'];

    protected $casts = ['foreign_amount' => 'decimal:2', 'book_rate' => 'decimal:10', 'closing_rate' => 'decimal:10', 'book_value' => 'decimal:2', 'closing_value' => 'decimal:2', 'gain_loss' => 'decimal:2', 'gain_status' => GainStatus::class, 'metadata' => 'array'];

    public function run(): BelongsTo
    {
        return $this->belongsTo(RevaluationRun::class, 'run_id');
    }
}
