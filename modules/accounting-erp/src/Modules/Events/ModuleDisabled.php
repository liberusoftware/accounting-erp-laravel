<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Modules\Events;

use Liberu\Accounting\Erp\Modules\Contracts\ModuleInterface;

final readonly class ModuleDisabled
{
    public function __construct(
        public string $name,
        public ModuleInterface $module,
    ) {}
}
