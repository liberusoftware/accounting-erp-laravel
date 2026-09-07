<?php

declare(strict_types=1);

namespace Liberu\Accounting\Vat\Enums;

enum VatDirection: string
{
    case Input = 'input';
    case Output = 'output';
}
