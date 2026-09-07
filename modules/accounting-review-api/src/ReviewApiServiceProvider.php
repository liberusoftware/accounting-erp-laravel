<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReviewApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\Review\Models\ReviewItem;
use Liberu\Accounting\ReviewApi\Policies\ReviewPolicy;

final class ReviewApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(ReviewItem::class, ReviewPolicy::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
