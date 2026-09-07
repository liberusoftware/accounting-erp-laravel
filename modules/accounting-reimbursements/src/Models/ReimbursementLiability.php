<?php

declare(strict_types=1);

namespace Liberu\Accounting\Reimbursements\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\Reimbursements\Enums\ReimbursementStatus;

/**
 * @property int $id
 * @property int|null $team_id
 * @property string $payee_ref
 * @property string $currency
 * @property float|string $amount
 * @property ReimbursementStatus $status
 * @property int|null $batch_id
 * @property array<string,mixed>|null $metadata
 */
final class ReimbursementLiability extends Model
{
    protected $table = 'accounting_reimbursement_liabilities';

    protected $fillable = ['team_id', 'payee_ref', 'source_type', 'source_id', 'kind', 'currency', 'amount', 'approved_at', 'status', 'batch_id', 'metadata'];

    protected $casts = ['status' => ReimbursementStatus::class, 'amount' => 'decimal:2', 'approved_at' => 'datetime', 'metadata' => 'array'];
}
