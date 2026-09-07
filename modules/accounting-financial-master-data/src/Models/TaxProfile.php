<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterData\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\FinancialMasterData\Enums\RecordStatus;

class TaxProfile extends Model
{
    protected $table = 'accounting_master_tax_profiles';

    protected $fillable = ['legal_entity_id', 'code', 'name', 'rate', 'inclusive', 'recoverable', 'status', 'metadata'];

    protected $attributes = ['inclusive' => false, 'recoverable' => true, 'status' => 'active'];

    protected $casts = ['rate' => 'decimal:4', 'inclusive' => 'bool', 'recoverable' => 'bool', 'status' => RecordStatus::class, 'metadata' => 'array'];
}
