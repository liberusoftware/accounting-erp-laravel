<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiEntity\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class MasterDataPolicy extends Model
{
    protected $table = 'accounting_multi_entity_policies';

    protected $fillable = ['entity_id', 'policy_key', 'mode', 'configuration', 'metadata'];

    protected $casts = ['configuration' => 'array', 'metadata' => 'array'];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(EntityBook::class, 'entity_id');
    }
}
