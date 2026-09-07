<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierPortal\Events;

use Liberu\Accounting\SupplierPortal\Models\PortalDocument;

final readonly class PortalDocumentAttached
{
    public function __construct(public PortalDocument $document) {}
}
