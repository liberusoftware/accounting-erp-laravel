<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxReturns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\TaxReturns\Enums\TaxReturnStatus;

final class TaxReturn extends Model
{
    protected $table = 'accounting_tax_returns';

    protected $fillable = ['team_id', 'tax_type', 'jurisdiction', 'period_start', 'period_end', 'due_on', 'status', 'external_reference', 'submitted_at', 'metadata'];

    protected $casts = ['period_start' => 'date', 'period_end' => 'date', 'due_on' => 'date', 'submitted_at' => 'datetime', 'status' => TaxReturnStatus::class, 'metadata' => 'array'];

    public function lines(): HasMany
    {
        return $this->hasMany(TaxReturnLine::class);
    }
}
