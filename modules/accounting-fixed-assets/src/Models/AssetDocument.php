<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property string $document_ref
 * @property string $kind
 * @property string $file_ref
 * @property string|null $description
 * @property string|null $checksum
 * @property Carbon $attached_at
 */
final class AssetDocument extends Model
{
    protected $table = 'accounting_fixed_asset_documents';

    protected $fillable = ['asset_id', 'document_ref', 'kind', 'file_ref', 'description', 'checksum', 'attached_by', 'attached_at', 'metadata'];

    protected $casts = ['attached_at' => 'datetime', 'metadata' => 'array'];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
