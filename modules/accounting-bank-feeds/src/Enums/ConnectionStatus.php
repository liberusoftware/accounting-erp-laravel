<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankFeeds\Enums;

enum ConnectionStatus: string
{
    case Active = 'active';
    case Paused = 'paused';
    case Error = 'error';
    case Revoked = 'revoked';
}
