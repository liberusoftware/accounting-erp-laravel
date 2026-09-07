<?php

declare(strict_types=1);

namespace Liberu\Accounting\ClientCollaborationApi;

use Illuminate\Support\ServiceProvider;

final class ClientCollaborationApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
