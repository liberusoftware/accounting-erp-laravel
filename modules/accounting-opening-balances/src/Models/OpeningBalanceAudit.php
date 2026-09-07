<?php

declare(strict_types=1);

namespace Liberu\Accounting\OpeningBalances\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class OpeningBalanceAudit extends Model
{
    public $timestamps = false;

    protected $table = 'accounting_opening_balance_audits';

    protected $fillable = ['batch_id', 'event_type', 'actor_id', 'payload', 'payload_hash', 'created_at'];

    protected $casts = ['payload' => 'array', 'created_at' => 'datetime'];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(OpeningBalanceBatch::class, 'batch_id');
    }
}
