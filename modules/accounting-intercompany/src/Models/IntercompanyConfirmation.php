<?php

declare(strict_types=1);

namespace Liberu\Accounting\Intercompany\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\Intercompany\Enums\ConfirmationStatus;

final class IntercompanyConfirmation extends Model
{
    protected $table = 'accounting_intercompany_confirmations';

    protected $fillable = ['transaction_id', 'entity_ref', 'status', 'confirmed_amount', 'comment', 'actor_ref', 'confirmed_at', 'metadata'];

    protected $casts = ['status' => ConfirmationStatus::class, 'confirmed_amount' => 'decimal:2', 'confirmed_at' => 'datetime', 'metadata' => 'array'];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(IntercompanyTransaction::class, 'transaction_id');
    }
}
