<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $source_type
 * @property string $source_id
 * @property string $snapshot_hash
 * @property-read TaxRule $taxRule
 */
final class TaxEvidence extends Model
{
    protected $table = 'accounting_tax_evidence';

    protected $fillable = ['tax_rule_id', 'source_type', 'source_id', 'snapshot_hash', 'snapshot', 'captured_by', 'metadata'];

    protected $casts = ['snapshot' => 'array', 'metadata' => 'array'];

    protected $hidden = ['snapshot_hash', 'snapshot'];

    public function taxRule(): BelongsTo
    {
        return $this->belongsTo(TaxRule::class, 'tax_rule_id');
    }
}
