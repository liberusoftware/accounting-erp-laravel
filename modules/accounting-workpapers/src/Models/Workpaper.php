<?php

declare(strict_types=1);

namespace Liberu\Accounting\Workpapers\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\Workpapers\Enums\WorkpaperStatus;

final class Workpaper extends Model
{
    protected $table = 'accounting_workpapers';

    protected $fillable = ['team_id', 'title', 'reference', 'status', 'period_start', 'period_end', 'preparer_id', 'reviewer_id', 'conclusion', 'metadata'];

    protected $casts = ['period_start' => 'date', 'period_end' => 'date', 'status' => WorkpaperStatus::class, 'metadata' => 'array'];

    public function references(): HasMany
    {
        return $this->hasMany(WorkpaperReference::class);
    }

    public function procedures(): HasMany
    {
        return $this->hasMany(WorkpaperProcedure::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(WorkpaperAttachment::class);
    }

    public function exports(): HasMany
    {
        return $this->hasMany(WorkpaperExport::class);
    }
}
