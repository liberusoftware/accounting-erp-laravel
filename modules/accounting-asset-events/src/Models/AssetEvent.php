<?php

declare(strict_types=1);

namespace Liberu\Accounting\AssetEvents\Models;

use Illuminate\Database\Eloquent\Model;

final class AssetEvent extends Model
{
    protected $table = 'accounting_asset_events';

    public $updated_at = false;

    protected $fillable = ['team_id', 'asset_id', 'event_type', 'event_date', 'description', 'source_type', 'source_id', 'old_values', 'new_values', 'evidence', 'actor_id'];

    protected $casts = ['event_date' => 'date', 'old_values' => 'array', 'new_values' => 'array', 'evidence' => 'array', 'actor_id' => 'integer'];

    protected static function booted(): void
    {
        self::updating(fn (): never => throw new \LogicException('Asset events are immutable.'));
        self::deleting(fn (): never => throw new \LogicException('Asset events are immutable.'));
    }
}
