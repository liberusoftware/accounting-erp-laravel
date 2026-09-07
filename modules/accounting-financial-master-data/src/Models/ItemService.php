<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterData\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\FinancialMasterData\Enums\RecordStatus;

class ItemService extends Model
{
    protected $table = 'accounting_master_items_services';

    protected $fillable = ['legal_entity_id', 'sku', 'name', 'description', 'kind', 'unit', 'sales_price', 'purchase_price', 'tax_profile_id', 'status', 'metadata'];

    protected $attributes = ['kind' => 'service', 'status' => 'active'];

    protected $casts = ['sales_price' => 'decimal:2', 'purchase_price' => 'decimal:2', 'status' => RecordStatus::class, 'metadata' => 'array'];
}
