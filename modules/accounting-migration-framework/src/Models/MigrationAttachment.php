<?php

declare(strict_types=1);

namespace Liberu\Accounting\MigrationFramework\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class MigrationAttachment extends Model
{
    protected $table = 'accounting_migration_attachments';

    protected $fillable = ['batch_id', 'file_ref', 'name', 'mime_type', 'size_bytes', 'checksum', 'metadata'];

    protected $casts = ['size_bytes' => 'integer', 'metadata' => 'array'];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(MigrationBatch::class, 'batch_id');
    }
}
