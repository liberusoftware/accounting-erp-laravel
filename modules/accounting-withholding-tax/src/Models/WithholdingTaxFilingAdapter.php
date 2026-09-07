<?php

declare(strict_types=1);

namespace Liberu\Accounting\WithholdingTax\Models;

use Illuminate\Database\Eloquent\Model;

final class WithholdingTaxFilingAdapter extends Model
{
    protected $table = 'accounting_withholding_tax_filing_adapters';

    protected $fillable = ['team_id', 'jurisdiction', 'provider', 'status', 'configuration'];

    protected $casts = ['configuration' => 'array'];
}
