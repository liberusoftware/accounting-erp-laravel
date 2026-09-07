<?php

declare(strict_types=1);

namespace Liberu\Accounting\EInvoicing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class EInvoiceEvent extends Model
{
    protected $table = 'accounting_e_invoice_events';

    protected $fillable = ['document_id', 'event', 'provider_ref', 'actor_ref', 'message', 'metadata'];

    protected $casts = ['metadata' => 'array'];

    public function document(): BelongsTo
    {
        return $this->belongsTo(EInvoiceDocument::class, 'document_id');
    }
}
