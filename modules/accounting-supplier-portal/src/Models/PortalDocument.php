<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierPortal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** @property int $id @property string $path @property string $original_name @property string $mime_type @property string $sha256 @property-read PortalResource $resource */
final class PortalDocument extends Model
{
    protected $table = 'accounting_supplier_portal_documents';

    protected $fillable = ['resource_id', 'path', 'original_name', 'mime_type', 'sha256', 'uploaded_by', 'metadata'];

    protected $casts = ['metadata' => 'array'];

    protected $hidden = ['path', 'sha256'];

    public function resource(): BelongsTo
    {
        return $this->belongsTo(PortalResource::class, 'resource_id');
    }
}
