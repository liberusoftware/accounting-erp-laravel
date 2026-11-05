<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $path
 */
class DocumentVersion extends Model
{
    use HasFactory;

    #[\Override]
    protected $fillable = ['document_id', 'version_number', 'path', 'original_filename', 'mime_type', 'size', 'uploaded_by'];

    #[\Override]
    protected $casts = ['version_number' => 'integer', 'size' => 'integer'];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
