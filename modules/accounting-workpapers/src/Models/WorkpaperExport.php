<?php

declare(strict_types=1);

namespace Liberu\Accounting\Workpapers\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class WorkpaperExport extends Model
{
    protected $table = 'accounting_workpaper_exports';

    protected $fillable = ['workpaper_id', 'format', 'status', 'path', 'error'];

    public function workpaper(): BelongsTo
    {
        return $this->belongsTo(Workpaper::class);
    }
}
