<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\AccountsPayable\Enums\DisputeStatus;

class PayableDispute extends Model
{
    protected $table = 'accounting_ap_disputes';

    protected $fillable = ['open_item_id', 'amount', 'reason', 'status', 'opened_at', 'resolved_at', 'resolution', 'metadata'];

    protected $casts = ['amount' => 'decimal:2', 'status' => DisputeStatus::class, 'opened_at' => 'datetime', 'resolved_at' => 'datetime', 'metadata' => 'array'];

    public function openItem(): BelongsTo
    {
        return $this->belongsTo(PayableOpenItem::class, 'open_item_id');
    }
}
