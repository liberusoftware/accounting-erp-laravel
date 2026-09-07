<?php

declare(strict_types=1);

namespace Liberu\Accounting\GoodsAndServiceReceipts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ReceiptAttachment extends Model
{
    protected $table = 'accounting_goods_service_receipt_attachments';

    protected $fillable = ['receipt_id', 'attachment_ref', 'kind', 'file_ref', 'description', 'checksum', 'attached_by', 'attached_at', 'metadata'];

    protected $casts = ['attached_at' => 'datetime', 'metadata' => 'array'];

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class, 'receipt_id');
    }
}
