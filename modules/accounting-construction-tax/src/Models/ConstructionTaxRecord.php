<?php

declare(strict_types=1);

namespace Liberu\Accounting\ConstructionTax\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\ConstructionTax\Enums\ConstructionTaxStatus;

final class ConstructionTaxRecord extends Model
{
    protected $table = 'accounting_construction_tax_records';

    protected $fillable = ['team_id', 'subcontractor_ref', 'verification_status', 'deduction_rate', 'tax_period', 'gross_amount', 'deduction_amount', 'return_status', 'filing_adapter', 'verification', 'statement', 'correction'];

    protected $casts = ['verification_status' => ConstructionTaxStatus::class, 'deduction_rate' => 'decimal:4', 'gross_amount' => 'decimal:8', 'deduction_amount' => 'decimal:8', 'verification' => 'array', 'statement' => 'array', 'correction' => 'array'];
}
