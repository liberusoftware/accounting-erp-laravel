<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierPortal\Events;

use Liberu\Accounting\SupplierPortal\Models\PortalResource;

final readonly class PortalResourceCreated
{
    public function __construct(public PortalResource $resource) {}
}
