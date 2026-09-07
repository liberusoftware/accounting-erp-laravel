<?php

declare(strict_types=1);

namespace Liberu\Accounting\WithholdingTax\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Liberu\Accounting\WithholdingTax\Enums\WithholdingStatus;

final class WithholdingTaxDeduction extends Model
{
    protected $table = 'accounting_withholding_tax_deductions';

    protected $fillable = ['team_id', 'rule_id', 'party_type', 'party_id', 'source_ref', 'currency', 'gross_amount', 'withheld_amount', 'status', 'metadata'];

    protected $casts = ['gross_amount' => 'decimal:2', 'withheld_amount' => 'decimal:2', 'status' => WithholdingStatus::class, 'metadata' => 'array'];

    public function liability(): HasOne
    {
        return $this->hasOne(WithholdingTaxLiability::class, 'deduction_id');
    }
}
