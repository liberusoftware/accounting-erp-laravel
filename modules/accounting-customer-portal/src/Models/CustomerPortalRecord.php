<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomerPortal\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\CustomerPortal\Enums\CustomerPortalRecordType;
use Liberu\Accounting\CustomerPortal\Enums\CustomerPortalStatus;

final class CustomerPortalRecord extends Model
{
    protected $table = 'accounting_customer_portal_records';

    protected $fillable = ['team_id', 'customer_id', 'type', 'reference', 'status', 'currency', 'amount', 'payload', 'metadata', 'published_at'];

    protected $casts = ['type' => CustomerPortalRecordType::class, 'status' => CustomerPortalStatus::class, 'amount' => 'decimal:8', 'payload' => 'array', 'metadata' => 'array', 'published_at' => 'datetime'];

    public function scopeForCustomer(Builder $query, string $customerId): Builder
    {
        return $query->where('customer_id', $customerId);
    }
}
