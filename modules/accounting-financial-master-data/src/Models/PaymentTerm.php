<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterData\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTerm extends Model
{
    protected $table = 'accounting_master_payment_terms';

    protected $fillable = ['legal_entity_id', 'code', 'name', 'days', 'status', 'metadata'];

    protected $attributes = ['days' => 0, 'status' => 'active'];

    protected $casts = ['days' => 'integer', 'metadata' => 'array'];
}
