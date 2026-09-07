<?php

declare(strict_types=1);

namespace Liberu\Accounting\CoreApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\Core\Models\Book;
use Liberu\Accounting\Core\Models\FiscalCalendar;
use Liberu\Accounting\Core\Models\LegalEntity;
use Liberu\Accounting\Core\Models\NumberingSequence;
use Liberu\Accounting\CoreApi\Policies\AccountingCorePolicy;
use Liberu\Accounting\CoreApi\Policies\LegalEntityPolicy;

final class AccountingCoreApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(LegalEntity::class, LegalEntityPolicy::class);
        foreach ([Book::class, FiscalCalendar::class, NumberingSequence::class] as $model) {
            Gate::policy($model, AccountingCorePolicy::class);
        }
        Route::model('legalEntity', LegalEntity::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
