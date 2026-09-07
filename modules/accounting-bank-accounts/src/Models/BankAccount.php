<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankAccounts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\BankAccounts\Enums\BankAccountStatus;
use Liberu\Accounting\BankAccounts\Enums\BankAccountType;
use Liberu\Accounting\Core\Models\LegalEntity;

final class BankAccount extends Model
{
    protected $table = 'accounting_bank_accounts';

    protected $fillable = ['legal_entity_id', 'owner_type', 'owner_id', 'name', 'institution_name', 'account_type', 'currency', 'opening_balance', 'opening_date', 'current_balance', 'account_number', 'routing_number', 'feed_reference', 'status', 'closed_at', 'metadata'];

    protected $hidden = ['account_number', 'routing_number'];

    protected $casts = ['account_type' => BankAccountType::class, 'status' => BankAccountStatus::class, 'opening_date' => 'date', 'closed_at' => 'datetime', 'opening_balance' => 'decimal:2', 'current_balance' => 'decimal:2', 'account_number' => 'encrypted', 'routing_number' => 'encrypted', 'metadata' => 'array'];

    protected $attributes = ['status' => 'active', 'opening_balance' => 0, 'current_balance' => 0];

    public function legalEntity(): BelongsTo
    {
        return $this->belongsTo(LegalEntity::class);
    }
}
