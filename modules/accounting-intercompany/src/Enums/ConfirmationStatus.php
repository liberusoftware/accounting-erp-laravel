<?php

declare(strict_types=1);

namespace Liberu\Accounting\Intercompany\Enums;

enum ConfirmationStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Rejected = 'rejected';
}
