<?php

declare(strict_types=1);

namespace Liberu\Accounting\ThreeWayMatching\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $match_id
 * @property string $source_type
 * @property string $source_id
 * @property string $snapshot_hash
 * @property-read MatchRecord $match
 */
final class MatchEvidence extends Model
{
    protected $table = 'accounting_three_way_match_evidence';

    protected $fillable = ['match_id', 'source_type', 'source_id', 'snapshot_hash', 'snapshot', 'captured_by', 'metadata'];

    protected $casts = ['snapshot' => 'array', 'metadata' => 'array'];

    protected $hidden = ['snapshot_hash', 'snapshot'];

    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchRecord::class, 'match_id');
    }
}
