<?php

declare(strict_types=1);

namespace Liberu\Accounting\WithholdingTax\Models;

use Illuminate\Database\Eloquent\Model;

final class WithholdingTaxRemittance extends Model
{
    protected $table = 'accounting_withholding_tax_remittances';

    protected $fillable = ['team_id', 'liability_id', 'amount', 'remitted_on', 'reference', 'status', 'metadata'];

    protected $casts = ['amount' => 'decimal:2', 'remitted_on' => 'date', 'metadata' => 'array'];
}
