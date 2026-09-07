<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterData\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankDetailReference extends Model
{
    protected $table = 'accounting_master_bank_detail_references';

    protected $fillable = ['party_id', 'label', 'account_name', 'bank_name', 'country_code', 'currency_code', 'masked_account', 'credential_reference', 'is_primary', 'status', 'metadata'];

    protected $attributes = ['is_primary' => false, 'status' => 'active'];

    protected $casts = ['is_primary' => 'bool', 'status' => 'string', 'metadata' => 'array'];

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }
}
