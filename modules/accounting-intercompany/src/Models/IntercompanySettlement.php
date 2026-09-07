<?php

declare(strict_types=1);

namespace Liberu\Accounting\Intercompany\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class IntercompanySettlement extends Model
{
    protected $table = 'accounting_intercompany_settlements';

    protected $fillable = ['transaction_id', 'settlement_ref', 'amount', 'currency', 'settled_at', 'source_ref', 'metadata'];

    protected $casts = ['amount' => 'decimal:2', 'settled_at' => 'datetime', 'metadata' => 'array'];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(IntercompanyTransaction::class, 'transaction_id');
    }
}
