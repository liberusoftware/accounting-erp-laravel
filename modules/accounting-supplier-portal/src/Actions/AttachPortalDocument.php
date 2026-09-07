<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierPortal\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\SupplierPortal\Events\PortalDocumentAttached;
use Liberu\Accounting\SupplierPortal\Models\PortalDocument;
use Liberu\Accounting\SupplierPortal\Models\PortalResource;

final class AttachPortalDocument
{
    public function handle(PortalResource $resource, array $attributes): PortalDocument
    {
        return DB::transaction(function () use ($resource, $attributes): PortalDocument {
            $document = PortalDocument::query()->firstOrCreate(['resource_id' => $resource->id, 'sha256' => $attributes['sha256']], array_merge($attributes, ['resource_id' => $resource->id]));
            DB::afterCommit(fn () => event(new PortalDocumentAttached($document)));

            return $document;
        });
    }
}
