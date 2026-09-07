<?php

declare(strict_types=1);

namespace Liberu\Accounting\Vat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class VatAdjustment extends Model
{
    protected $table = 'accounting_vat_adjustments';

    protected $fillable = ['vat_return_id', 'box', 'amount', 'reason', 'created_by'];

    protected $casts = ['amount' => 'decimal:6'];

    public function vatReturn(): BelongsTo
    {
        return $this->belongsTo(VatReturn::class);
    }
}
