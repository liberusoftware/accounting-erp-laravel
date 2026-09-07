<?php

declare(strict_types=1);

namespace Liberu\Accounting\Workpapers\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class WorkpaperAttachment extends Model
{
    protected $table = 'accounting_workpaper_attachments';

    protected $fillable = ['workpaper_id', 'name', 'disk', 'path', 'mime_type', 'size'];

    public function workpaper(): BelongsTo
    {
        return $this->belongsTo(Workpaper::class);
    }
}
