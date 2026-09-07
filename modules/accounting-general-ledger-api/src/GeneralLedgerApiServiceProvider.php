<?php

declare(strict_types=1);

namespace Liberu\Accounting\GeneralLedgerApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\GeneralLedger\Models\JournalEntry;
use Liberu\Accounting\GeneralLedgerApi\Policies\GeneralLedgerPolicy;

final class GeneralLedgerApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(JournalEntry::class, GeneralLedgerPolicy::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
