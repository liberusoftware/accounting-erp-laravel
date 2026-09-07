<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiEntity\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\MultiEntity\Enums\EntityPeriodStatus;

final class EntityPeriod extends Model
{
    protected $table = 'accounting_multi_entity_periods';

    protected $fillable = ['entity_id', 'period_ref', 'starts_on', 'ends_on', 'tax_configuration', 'status', 'closed_by', 'closed_at', 'metadata'];

    protected $casts = ['starts_on' => 'date', 'ends_on' => 'date', 'tax_configuration' => 'array', 'status' => EntityPeriodStatus::class, 'closed_at' => 'datetime', 'metadata' => 'array'];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(EntityBook::class, 'entity_id');
    }
}
