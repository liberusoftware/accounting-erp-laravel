<?php

declare(strict_types=1);

namespace Liberu\Accounting\Vat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\Vat\Enums\VatReturnStatus;

final class VatReturn extends Model
{
    protected $table = 'accounting_vat_returns';

    protected $fillable = ['team_id', 'period_start', 'period_end', 'scheme', 'status', 'boxes', 'submitted_at', 'submission_ref', 'metadata'];

    protected $casts = ['period_start' => 'date', 'period_end' => 'date', 'status' => VatReturnStatus::class, 'boxes' => 'array', 'submitted_at' => 'datetime', 'metadata' => 'array'];

    public function adjustments(): HasMany
    {
        return $this->hasMany(VatAdjustment::class);
    }
}
