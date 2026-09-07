<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierPortal\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Liberu\Accounting\SupplierPortal\Enums\PortalResourceType;
use Liberu\Accounting\SupplierPortal\Enums\PortalStatus;

/**
 * @property int $id
 * @property string $supplier_id
 * @property PortalResourceType $type
 * @property string $reference
 * @property PortalStatus $status
 * @property string $currency
 * @property float|string $amount
 * @property array<string,mixed>|null $payload
 * @property Carbon|null $submitted_at
 * @property Carbon|null $approved_at
 * @property string|null $rejected_reason
 * @property-read Collection<int, PortalDocument> $documents
 */
final class PortalResource extends Model
{
    protected $table = 'accounting_supplier_portal_resources';

    protected $fillable = ['supplier_id', 'type', 'reference', 'status', 'currency', 'amount', 'payload', 'submitted_at', 'approved_at', 'rejected_reason', 'metadata'];

    protected $casts = ['type' => PortalResourceType::class, 'status' => PortalStatus::class, 'amount' => 'decimal:2', 'payload' => 'array', 'metadata' => 'array', 'submitted_at' => 'datetime', 'approved_at' => 'datetime'];

    public function documents(): HasMany
    {
        return $this->hasMany(PortalDocument::class, 'resource_id');
    }

    public function scopeForSupplier(Builder $query, string $supplierId): Builder
    {
        return $query->where('supplier_id', $supplierId);
    }
}
