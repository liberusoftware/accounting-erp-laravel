<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiCurrency\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Liberu\Accounting\MultiCurrency\Enums\RevaluationStatus;

/**
 * @property string $run_ref
 * @property string $functional_currency
 * @property string $source_hash
 * @property Carbon|null $as_of_date
 * @property RevaluationStatus $status
 * @property array<string, mixed>|null $summary
 */
final class RevaluationRun extends Model
{
    protected $table = 'accounting_multi_currency_revaluations';

    protected $fillable = ['team_id', 'run_ref', 'scope_ref', 'as_of_date', 'functional_currency', 'status', 'source_hash', 'realized_gain', 'realized_loss', 'unrealized_gain', 'unrealized_loss', 'summary', 'failure_message', 'requested_by', 'posted_by', 'posted_at', 'metadata'];

    protected $casts = ['as_of_date' => 'date', 'status' => RevaluationStatus::class, 'realized_gain' => 'decimal:2', 'realized_loss' => 'decimal:2', 'unrealized_gain' => 'decimal:2', 'unrealized_loss' => 'decimal:2', 'summary' => 'array', 'posted_at' => 'datetime', 'metadata' => 'array'];

    public function positions(): HasMany
    {
        return $this->hasMany(RevaluationPosition::class, 'run_id');
    }

    public function reconciliations(): HasMany
    {
        return $this->hasMany(CurrencyReconciliation::class, 'run_id');
    }
}
