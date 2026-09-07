<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountReconciliations\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\AccountReconciliations\Enums\ReconciliationStatus;

final class AccountReconciliation extends Model
{
    protected $table = 'accounting_account_reconciliations';

    protected $fillable = ['team_id', 'book_id', 'account_id', 'period_start', 'period_end', 'status', 'template', 'source_balance', 'supporting_items', 'preparer', 'reviewer', 'aging', 'certification', 'carry_forward'];

    protected $casts = ['status' => ReconciliationStatus::class, 'period_start' => 'date', 'period_end' => 'date', 'template' => 'array', 'source_balance' => 'array', 'supporting_items' => 'array', 'preparer' => 'array', 'reviewer' => 'array', 'aging' => 'array', 'certification' => 'array', 'carry_forward' => 'array'];

    public function isCertified(): bool
    {
        return $this->status === ReconciliationStatus::Certified;
    }
}
