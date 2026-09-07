<?php

declare(strict_types=1);

namespace Liberu\Accounting\ClientCollaboration;

use Illuminate\Support\ServiceProvider;

final class ClientCollaborationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
