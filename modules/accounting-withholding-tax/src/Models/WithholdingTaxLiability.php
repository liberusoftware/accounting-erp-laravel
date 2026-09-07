<?php

declare(strict_types=1);

namespace Liberu\Accounting\WithholdingTax\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\WithholdingTax\Enums\WithholdingStatus;

final class WithholdingTaxLiability extends Model
{
    protected $table = 'accounting_withholding_tax_liabilities';

    protected $fillable = ['team_id', 'deduction_id', 'amount', 'due_on', 'status', 'metadata'];

    protected $casts = ['amount' => 'decimal:2', 'due_on' => 'date', 'status' => WithholdingStatus::class, 'metadata' => 'array'];
}
