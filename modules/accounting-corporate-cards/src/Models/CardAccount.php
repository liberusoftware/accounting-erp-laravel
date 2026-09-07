<?php

declare(strict_types=1);

namespace Liberu\Accounting\CorporateCards\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class CardAccount extends Model
{
    protected $table = 'accounting_corporate_card_accounts';

    protected $fillable = ['team_id', 'card_ref', 'holder_ref', 'provider_ref', 'currency', 'limit_amount', 'spent_amount', 'status', 'controls'];

    protected $casts = ['limit_amount' => 'decimal:8', 'spent_amount' => 'decimal:8', 'controls' => 'array'];

    public function transactions(): HasMany
    {
        return $this->hasMany(CardTransaction::class, 'card_account_id');
    }
}
