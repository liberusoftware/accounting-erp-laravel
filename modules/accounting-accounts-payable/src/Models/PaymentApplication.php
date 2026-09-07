<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentApplication extends Model
{
    protected $table = 'accounting_ap_payment_applications';

    protected $fillable = ['payment_id', 'open_item_id', 'amount'];

    protected $casts = ['amount' => 'decimal:2'];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(PayablePayment::class, 'payment_id');
    }

    public function openItem(): BelongsTo
    {
        return $this->belongsTo(PayableOpenItem::class, 'open_item_id');
    }
}
