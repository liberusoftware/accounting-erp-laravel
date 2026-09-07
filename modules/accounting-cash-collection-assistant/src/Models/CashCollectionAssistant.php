<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashCollectionAssistant\Models;

use Illuminate\Database\Eloquent\Model;

final class CashCollectionAssistant extends Model
{
    protected $table = 'accounting_cash_collection_assistants';

    protected $fillable = ['team_id', 'invoice_ref', 'customer_ref', 'risk_score', 'risk_level', 'reminder_status', 'reminder_at', 'promised_amount', 'promised_date', 'promise_status', 'policy_ref', 'approval_status', 'outcome', 'metadata'];

    protected $casts = ['risk_score' => 'integer', 'reminder_at' => 'datetime', 'promised_date' => 'date', 'promised_amount' => 'decimal:8', 'metadata' => 'array'];
}
