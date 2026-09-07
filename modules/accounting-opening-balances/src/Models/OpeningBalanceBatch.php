<?php

declare(strict_types=1);

namespace Liberu\Accounting\OpeningBalances\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\OpeningBalances\Enums\OpeningBalanceStatus;

/**
 * @property int $id
 * @property int|null $team_id
 * @property string|null $currency
 * @property string $source_hash
 * @property string|null $failure_message
 * @property OpeningBalanceStatus $status
 * @property array<string,mixed>|null $summary
 */
final class OpeningBalanceBatch extends Model
{
    protected $table = 'accounting_opening_balance_batches';

    protected $fillable = ['team_id', 'batch_ref', 'migration_date', 'currency', 'status', 'source_hash', 'idempotency_key', 'summary', 'failure_message', 'requested_by', 'approved_by', 'approved_at', 'metadata'];

    protected $casts = ['migration_date' => 'date', 'status' => OpeningBalanceStatus::class, 'summary' => 'array', 'approved_at' => 'datetime', 'metadata' => 'array'];

    /** @return HasMany<OpeningBalanceEntry, $this> */
    public function entries(): HasMany
    {
        return $this->hasMany(OpeningBalanceEntry::class, 'batch_id');
    }

    /** @return HasMany<OpeningBalanceReconciliation, $this> */
    public function reconciliations(): HasMany
    {
        return $this->hasMany(OpeningBalanceReconciliation::class, 'batch_id');
    }

    /** @return HasMany<OpeningBalanceAudit, $this> */
    public function audits(): HasMany
    {
        return $this->hasMany(OpeningBalanceAudit::class, 'batch_id');
    }
}
