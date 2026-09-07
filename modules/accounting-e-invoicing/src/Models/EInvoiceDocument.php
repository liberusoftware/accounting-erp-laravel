<?php

declare(strict_types=1);

namespace Liberu\Accounting\EInvoicing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\EInvoicing\Enums\DocumentStatus;

/**
 * @property DocumentStatus $status
 * @property string $format
 * @property string $document_ref
 * @property string|null $provider_ref
 * @property array<string, mixed> $payload
 */
final class EInvoiceDocument extends Model
{
    protected $table = 'accounting_e_invoice_documents';

    protected $fillable = ['legal_entity_id', 'document_ref', 'document_type', 'format', 'status', 'tax_id', 'counterparty_ref', 'currency', 'provider_ref', 'payload', 'signature', 'submitted_at', 'received_at', 'archived_at', 'metadata'];

    protected $casts = ['status' => DocumentStatus::class, 'payload' => 'array', 'metadata' => 'array', 'submitted_at' => 'datetime', 'received_at' => 'datetime', 'archived_at' => 'datetime'];

    /** @return HasMany<EInvoiceEvent, $this> */
    public function events(): HasMany
    {
        return $this->hasMany(EInvoiceEvent::class, 'document_id');
    }
}
