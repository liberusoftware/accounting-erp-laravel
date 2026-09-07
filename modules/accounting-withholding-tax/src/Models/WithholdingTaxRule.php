<?php

declare(strict_types=1);

namespace Liberu\Accounting\WithholdingTax\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\WithholdingTax\Enums\WithholdingStatus;

final class WithholdingTaxRule extends Model
{
    protected $table = 'accounting_withholding_tax_rules';

    protected $fillable = ['team_id', 'code', 'name', 'jurisdiction', 'rate', 'threshold', 'effective_from', 'effective_until', 'status', 'metadata'];

    protected $casts = ['rate' => 'decimal:4', 'threshold' => 'decimal:2', 'effective_from' => 'date', 'effective_until' => 'date', 'status' => WithholdingStatus::class, 'metadata' => 'array'];

    public function deductions(): HasMany
    {
        return $this->hasMany(WithholdingTaxDeduction::class, 'rule_id');
    }
}
