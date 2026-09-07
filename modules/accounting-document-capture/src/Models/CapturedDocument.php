<?php

declare(strict_types=1);

namespace Liberu\Accounting\DocumentCapture\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\DocumentCapture\Enums\CaptureStatus;

/**
 * @property int|null $team_id
 * @property CaptureStatus $status
 * @property string $source_channel
 * @property string $file_ref
 * @property float $confidence
 */
final class CapturedDocument extends Model
{
    protected $table = 'accounting_captured_documents';

    protected $fillable = ['team_id', 'source_channel', 'file_ref', 'checksum', 'mime_type', 'status', 'supplier_ref', 'document_ref', 'extracted_data', 'confidence', 'duplicate_of', 'retention_until', 'reviewed_by', 'reviewed_at', 'rejection_reason', 'metadata'];

    protected $casts = ['status' => CaptureStatus::class, 'extracted_data' => 'array', 'metadata' => 'array', 'confidence' => 'float', 'retention_until' => 'date', 'reviewed_at' => 'datetime'];

    /** @return HasMany<CaptureEvent, $this> */
    public function events(): HasMany
    {
        return $this->hasMany(CaptureEvent::class, 'document_id');
    }
}
