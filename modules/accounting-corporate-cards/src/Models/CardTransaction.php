<?php

declare(strict_types=1);

namespace Liberu\Accounting\CorporateCards\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\CorporateCards\Enums\CardTransactionStatus;

final class CardTransaction extends Model
{
    protected $table = 'accounting_corporate_card_transactions';

    protected $fillable = ['card_account_id', 'team_id', 'transaction_ref', 'transaction_date', 'amount', 'currency', 'merchant_ref', 'status', 'category_ref', 'receipt_ref', 'feed_ref', 'approved_by', 'approved_at', 'reconciliation_ref', 'metadata'];

    protected $casts = ['transaction_date' => 'date', 'amount' => 'decimal:8', 'status' => CardTransactionStatus::class, 'approved_at' => 'datetime', 'metadata' => 'array'];

    public function cardAccount(): BelongsTo
    {
        return $this->belongsTo(CardAccount::class, 'card_account_id');
    }
}
