<?php

declare(strict_types=1);

namespace Liberu\Accounting\JournalApprovals\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class JournalEvidence extends Model
{
    protected $table = 'accounting_journal_evidence';

    protected $fillable = ['approval_id', 'kind', 'file_ref', 'description', 'checksum', 'metadata'];

    protected $casts = ['metadata' => 'array'];

    public function approval(): BelongsTo
    {
        return $this->belongsTo(JournalApproval::class, 'approval_id');
    }
}
