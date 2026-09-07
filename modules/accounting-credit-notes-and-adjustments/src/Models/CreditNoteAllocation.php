<?php

declare(strict_types=1);

namespace Liberu\Accounting\CreditNotesAndAdjustments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CreditNoteAllocation extends Model
{
    protected $table = 'accounting_credit_note_allocations';

    protected $fillable = ['credit_note_id', 'team_id', 'invoice_ref', 'amount'];

    protected $casts = ['amount' => 'decimal:8'];

    public function creditNote(): BelongsTo
    {
        return $this->belongsTo(CreditNote::class, 'credit_note_id');
    }
}
