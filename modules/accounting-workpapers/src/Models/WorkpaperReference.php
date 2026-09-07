<?php

declare(strict_types=1);

namespace Liberu\Accounting\Workpapers\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class WorkpaperReference extends Model
{
    protected $table = 'accounting_workpaper_references';

    protected $fillable = ['workpaper_id', 'label', 'target_type', 'target_id', 'notes'];

    public function workpaper(): BelongsTo
    {
        return $this->belongsTo(Workpaper::class);
    }
}
