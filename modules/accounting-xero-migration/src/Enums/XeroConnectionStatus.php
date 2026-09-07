<?php

declare(strict_types=1);

namespace Liberu\Accounting\XeroMigration\Enums;

enum XeroConnectionStatus: string
{
    case Active = 'active';
    case Expired = 'expired';
    case Revoked = 'revoked';
}
