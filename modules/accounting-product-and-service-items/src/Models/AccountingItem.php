<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProductAndServiceItems\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\ProductAndServiceItems\Enums\ItemKind;
use Liberu\Accounting\ProductAndServiceItems\Enums\ItemStatus;

/** @property int $id @property string $code @property ItemKind $kind @property ItemStatus $status @property float|string|null $sales_price @property float|string|null $purchase_price */
final class AccountingItem extends Model
{
    protected $table = 'accounting_product_service_items';

    protected $fillable = ['team_id', 'code', 'name', 'kind', 'purchase_description', 'sales_description', 'sales_account_ref', 'purchase_account_ref', 'tax_default_ref', 'unit', 'purchase_price', 'sales_price', 'currency', 'status', 'ecommerce_refs', 'metadata'];

    protected $casts = ['kind' => ItemKind::class, 'status' => ItemStatus::class, 'purchase_price' => 'decimal:4', 'sales_price' => 'decimal:4', 'ecommerce_refs' => 'array', 'metadata' => 'array'];
}
