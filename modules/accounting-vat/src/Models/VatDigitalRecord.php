<?php

declare(strict_types=1);

namespace Liberu\Accounting\Vat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class VatDigitalRecord extends Model
{
    protected $table = 'accounting_vat_digital_records';

    protected $fillable = ['vat_record_id', 'record_hash', 'payload', 'recorded_at'];

    protected $casts = ['payload' => 'array', 'recorded_at' => 'datetime'];

    public function vatRecord(): BelongsTo
    {
        return $this->belongsTo(VatRecord::class);
    }
}
