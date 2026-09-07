<?php

declare(strict_types=1);

namespace Liberu\Accounting\CopilotApi;

use Illuminate\Support\ServiceProvider;

final class CopilotApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
