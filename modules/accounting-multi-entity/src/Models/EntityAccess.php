<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiEntity\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class EntityAccess extends Model
{
    protected $table = 'accounting_multi_entity_access';

    protected $fillable = ['entity_id', 'user_ref', 'role', 'permissions', 'is_default', 'metadata'];

    protected $casts = ['permissions' => 'array', 'is_default' => 'boolean', 'metadata' => 'array'];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(EntityBook::class, 'entity_id');
    }
}
