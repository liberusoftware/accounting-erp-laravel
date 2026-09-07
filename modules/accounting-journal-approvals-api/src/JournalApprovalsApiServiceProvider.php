<?php

declare(strict_types=1);

namespace Liberu\Accounting\JournalApprovalsApi;

use Illuminate\Support\ServiceProvider;

final class JournalApprovalsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
