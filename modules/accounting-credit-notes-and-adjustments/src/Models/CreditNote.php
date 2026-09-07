<?php

declare(strict_types=1);

namespace Liberu\Accounting\CreditNotesAndAdjustments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\CreditNotesAndAdjustments\Enums\CreditNoteStatus;

final class CreditNote extends Model
{
    protected $table = 'accounting_credit_notes';

    protected $fillable = ['team_id', 'customer_id', 'credit_ref', 'status', 'reason', 'currency', 'amount', 'allocated_amount', 'tax_amount', 'refund_reference', 'store_credit_reference', 'approved_by', 'approved_at', 'evidence', 'metadata'];

    protected $casts = ['status' => CreditNoteStatus::class, 'amount' => 'decimal:8', 'allocated_amount' => 'decimal:8', 'tax_amount' => 'decimal:8', 'approved_at' => 'datetime', 'evidence' => 'array', 'metadata' => 'array'];

    public function allocations(): HasMany
    {
        return $this->hasMany(CreditNoteAllocation::class, 'credit_note_id');
    }
}
