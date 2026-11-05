<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Listeners;

use Illuminate\Auth\Events\Registered;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Accounting\Erp\Services\TeamManagementService;

class CreatePersonalTeam
{
    public function __construct(protected TeamManagementService $teamManagementService) {}

    public function handle(Registered $event): void
    {
        if ($event->user instanceof User) {
            $this->teamManagementService->assignUserToDefaultTeam($event->user);
        }
    }
}
