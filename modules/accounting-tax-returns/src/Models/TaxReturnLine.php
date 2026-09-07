<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxReturns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class TaxReturnLine extends Model
{
    protected $table = 'accounting_tax_return_lines';

    protected $fillable = ['team_id', 'tax_return_id', 'code', 'amount', 'currency', 'metadata'];

    protected $casts = ['amount' => 'decimal:6', 'metadata' => 'array'];

    public function taxReturn(): BelongsTo
    {
        return $this->belongsTo(TaxReturn::class);
    }
}
