<?php

declare(strict_types=1);

namespace Liberu\Accounting\WithholdingTax\Models;

use Illuminate\Database\Eloquent\Model;

final class WithholdingTaxStatement extends Model
{
    protected $table = 'accounting_withholding_tax_statements';

    protected $fillable = ['team_id', 'jurisdiction', 'period_start', 'period_end', 'total_amount', 'status', 'payload'];

    protected $casts = ['period_start' => 'date', 'period_end' => 'date', 'total_amount' => 'decimal:2', 'payload' => 'array'];
}
