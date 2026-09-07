<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashCoding\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\CashCoding\Enums\CashCodingStatus;

final class CashCodingBatch extends Model
{
    protected $table = 'accounting_cash_coding_batches';

    protected $fillable = ['team_id', 'reference', 'status', 'lines', 'payee_creation_policy', 'total_amount', 'currency', 'created_by', 'reviewed_by', 'posted_by', 'undo_reason', 'posted_at', 'undone_at', 'metadata'];

    protected $casts = ['status' => CashCodingStatus::class, 'lines' => 'array', 'total_amount' => 'decimal:8', 'posted_at' => 'datetime', 'undone_at' => 'datetime', 'metadata' => 'array'];

    protected $hidden = ['metadata'];
}
