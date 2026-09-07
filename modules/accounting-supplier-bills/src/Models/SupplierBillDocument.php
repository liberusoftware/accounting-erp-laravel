<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBills\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $original_name
 * @property string $mime_type
 */
final class SupplierBillDocument extends Model
{
    protected $table = 'accounting_supplier_bill_documents';

    protected $fillable = ['bill_id', 'path', 'original_name', 'mime_type', 'sha256', 'metadata'];

    protected $casts = ['metadata' => 'array'];

    protected $hidden = ['path', 'sha256'];

    public function bill(): BelongsTo
    {
        return $this->belongsTo(SupplierBill::class, 'bill_id');
    }
}
