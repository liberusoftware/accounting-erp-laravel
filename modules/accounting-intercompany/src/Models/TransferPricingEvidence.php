<?php

declare(strict_types=1);

namespace Liberu\Accounting\Intercompany\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class TransferPricingEvidence extends Model
{
    protected $table = 'accounting_intercompany_transfer_pricing_evidence';

    protected $fillable = ['transaction_id', 'evidence_ref', 'kind', 'file_ref', 'description', 'arm_length_value', 'currency', 'captured_at', 'metadata'];

    protected $casts = ['arm_length_value' => 'decimal:2', 'captured_at' => 'datetime', 'metadata' => 'array'];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(IntercompanyTransaction::class, 'transaction_id');
    }
}
