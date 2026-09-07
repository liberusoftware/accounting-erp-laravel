<?php

declare(strict_types=1);

namespace Liberu\Accounting\Workpapers\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\Workpapers\Enums\ProcedureStatus;

final class WorkpaperProcedure extends Model
{
    protected $table = 'accounting_workpaper_procedures';

    protected $fillable = ['workpaper_id', 'description', 'status', 'performed_by', 'performed_at', 'evidence'];

    protected $casts = ['status' => ProcedureStatus::class, 'performed_at' => 'datetime'];

    public function workpaper(): BelongsTo
    {
        return $this->belongsTo(Workpaper::class);
    }
}
