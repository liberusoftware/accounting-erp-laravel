<?php

declare(strict_types=1);

namespace Liberu\Accounting\CreditNotesAndAdjustmentsApi;

use Illuminate\Support\ServiceProvider;

final class CreditNotesAndAdjustmentsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
