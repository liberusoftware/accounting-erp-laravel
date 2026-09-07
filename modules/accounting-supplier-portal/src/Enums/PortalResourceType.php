<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierPortal\Enums;

enum PortalResourceType: string
{
    case ProfileChange = 'profile_change';
    case PurchaseOrder = 'purchase_order';
    case Invoice = 'invoice';
    case Statement = 'statement';
    case Dispute = 'dispute';
    case PaymentStatus = 'payment_status';
    case Document = 'document';
}
