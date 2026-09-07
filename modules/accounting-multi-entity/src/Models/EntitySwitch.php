<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiEntity\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class EntitySwitch extends Model
{
    protected $table = 'accounting_multi_entity_switches';

    protected $fillable = ['entity_id', 'user_ref', 'session_ref', 'switched_at', 'metadata'];

    protected $casts = ['switched_at' => 'datetime', 'metadata' => 'array'];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(EntityBook::class, 'entity_id');
    }
}
