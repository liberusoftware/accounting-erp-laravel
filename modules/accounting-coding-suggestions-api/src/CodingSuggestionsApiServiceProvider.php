<?php

declare(strict_types=1);

namespace Liberu\Accounting\CodingSuggestionsApi;

use Illuminate\Support\ServiceProvider;

final class CodingSuggestionsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
