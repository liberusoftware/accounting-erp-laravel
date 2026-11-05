<?php

namespace Liberu\Accounting\Erp;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;
use Liberu\Accounting\Erp\Modules\ModuleManager;
use Liberu\Accounting\Erp\Modules\ModuleServiceProvider;
use Liberu\Accounting\Erp\Providers\EventServiceProvider;
use Liberu\Accounting\Erp\Providers\FortifyServiceProvider;
use Liberu\Accounting\Erp\Providers\JetstreamServiceProvider;

final class ErpServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ModuleManager::class);
        $this->app->register(ModuleServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
        $this->app->register(FortifyServiceProvider::class);
        $this->app->register(JetstreamServiceProvider::class);
    }

    public function boot(): void
    {
        Jetstream::useUserModel(Models\User::class);
        Jetstream::useTeamModel(Models\Team::class);
        Jetstream::useMembershipModel(Models\Membership::class);
        Jetstream::useTeamInvitationModel(Models\TeamInvitation::class);
        Relation::morphMap(require __DIR__.'/../morph-map.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-erp');

        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\Commands\BackfillDocuments::class,
                Console\Commands\BackfillTenancy::class,
                Console\Commands\CalculateLateFees::class,
                Console\Commands\ModuleCommand::class,
                Console\Commands\PostInvoice::class,
                Console\Commands\PostPayment::class,
                Console\Commands\ProcessRecurringTransactions::class,
                Console\Commands\ProcessSubscriptions::class,
                Console\Commands\ProvisionTenantChart::class,
                Console\Commands\PruneDocuments::class,
                Console\Commands\RecognizeRevenue::class,
                Console\Commands\SendPaymentReminders::class,
                Console\Commands\SyncPlaidTransactions::class,
                Console\Commands\UnpostInvoice::class,
                Console\Commands\UnpostPayment::class,
            ]);
        }
    }
}
