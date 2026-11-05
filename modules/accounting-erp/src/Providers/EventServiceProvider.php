<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Liberu\Accounting\Erp\Events\ApprovableApproved;
use Liberu\Accounting\Erp\Listeners\SendApprovedOutboundPayment;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    #[\Override]
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        // Execute an outbound payment once its approval clears (auto-approve
        // below threshold, or the final approver above it). No-op for other
        // approvables.
        ApprovableApproved::class => [
            SendApprovedOutboundPayment::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    #[\Override]
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    #[\Override]
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
