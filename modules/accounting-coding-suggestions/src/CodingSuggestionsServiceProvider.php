<?php

declare(strict_types=1);

namespace Liberu\Accounting\CodingSuggestions;

use Illuminate\Support\ServiceProvider;

final class CodingSuggestionsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
