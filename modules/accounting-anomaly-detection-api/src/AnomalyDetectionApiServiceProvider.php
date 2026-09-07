<?php

declare(strict_types=1);

namespace Liberu\Accounting\AnomalyDetectionApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\AnomalyDetection\Models\Anomaly;
use Liberu\Accounting\AnomalyDetectionApi\Policies\AnomalyPolicy;

final class AnomalyDetectionApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(Anomaly::class, AnomalyPolicy::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
