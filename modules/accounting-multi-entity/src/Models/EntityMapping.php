<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiEntity\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class EntityMapping extends Model
{
    protected $table = 'accounting_multi_entity_mappings';

    protected $fillable = ['entity_id', 'mapping_type', 'source_ref', 'target_ref', 'description', 'is_active', 'metadata'];

    protected $casts = ['is_active' => 'boolean', 'metadata' => 'array'];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(EntityBook::class, 'entity_id');
    }
}
