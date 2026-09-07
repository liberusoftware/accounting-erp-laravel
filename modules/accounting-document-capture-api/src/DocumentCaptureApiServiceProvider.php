<?php

declare(strict_types=1);

namespace Liberu\Accounting\DocumentCaptureApi;

use Illuminate\Support\ServiceProvider;

final class DocumentCaptureApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
