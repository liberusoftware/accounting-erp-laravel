<?php

declare(strict_types=1);

namespace Liberu\Accounting\DocumentCapture\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CaptureEvent extends Model
{
    protected $table = 'accounting_capture_events';

    protected $fillable = ['document_id', 'event', 'actor_ref', 'adapter_ref', 'message', 'metadata'];

    protected $casts = ['metadata' => 'array'];

    public function document(): BelongsTo
    {
        return $this->belongsTo(CapturedDocument::class, 'document_id');
    }
}
