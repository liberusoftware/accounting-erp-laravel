<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesInvoicing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesInvoiceDeposit extends Model
{
    protected $table = 'accounting_sales_invoice_deposits';

    protected $fillable = ['invoice_id', 'amount', 'currency', 'reference', 'received_by'];

    protected $casts = ['amount' => 'decimal:2'];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(SalesInvoice::class, 'invoice_id');
    }
}
