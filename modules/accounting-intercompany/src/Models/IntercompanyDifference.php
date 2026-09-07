<?php

declare(strict_types=1);

namespace Liberu\Accounting\Intercompany\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class IntercompanyDifference extends Model
{
    protected $table = 'accounting_intercompany_differences';

    protected $fillable = ['transaction_id', 'difference_ref', 'amount', 'reason', 'status', 'actor_ref', 'resolved_at', 'metadata'];

    protected $casts = ['amount' => 'decimal:2', 'resolved_at' => 'datetime', 'metadata' => 'array'];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(IntercompanyTransaction::class, 'transaction_id');
    }
}
