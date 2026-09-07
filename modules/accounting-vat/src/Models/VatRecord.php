<?php

declare(strict_types=1);

namespace Liberu\Accounting\Vat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Liberu\Accounting\Vat\Enums\VatDirection;
use Liberu\Accounting\Vat\Enums\VatRecordStatus;

final class VatRecord extends Model
{
    protected $table = 'accounting_vat_records';

    protected $fillable = ['team_id', 'direction', 'tax_code', 'net_amount', 'tax_amount', 'tax_rate', 'reverse_charge', 'scheme', 'box', 'source_type', 'source_id', 'status', 'occurred_on', 'evidence', 'metadata'];

    protected $casts = ['direction' => VatDirection::class, 'status' => VatRecordStatus::class, 'net_amount' => 'decimal:6', 'tax_amount' => 'decimal:6', 'tax_rate' => 'decimal:6', 'reverse_charge' => 'boolean', 'occurred_on' => 'date', 'metadata' => 'array'];

    public function digitalRecord(): HasOne
    {
        return $this->hasOne(VatDigitalRecord::class);
    }
}
