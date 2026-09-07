<?php

declare(strict_types=1);

namespace Liberu\Accounting\Reimbursements\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Liberu\Accounting\Reimbursements\Enums\BatchStatus;

/**
 * @property int $id
 * @property string $currency
 * @property float|string $total_amount
 * @property BatchStatus $status
 * @property string|null $provider
 * @property string|null $provider_ref
 * @property Carbon|null $submitted_at
 * @property Carbon|null $paid_at
 * @property array<string,mixed>|null $metadata
 */
final class ReimbursementBatch extends Model
{
    protected $table = 'accounting_reimbursement_batches';

    protected $fillable = ['team_id', 'currency', 'total_amount', 'status', 'provider', 'provider_ref', 'exported_at', 'submitted_at', 'paid_at', 'failure_message', 'metadata'];

    protected $casts = ['status' => BatchStatus::class, 'total_amount' => 'decimal:2', 'exported_at' => 'datetime', 'submitted_at' => 'datetime', 'paid_at' => 'datetime', 'metadata' => 'array'];

    public function liabilities(): HasMany
    {
        return $this->hasMany(ReimbursementLiability::class, 'batch_id');
    }
}
