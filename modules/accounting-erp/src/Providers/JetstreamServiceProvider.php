<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;
use Liberu\Accounting\Erp\Actions\Jetstream\AddTeamMember;
use Liberu\Accounting\Erp\Actions\Jetstream\CreateTeam;
use Liberu\Accounting\Erp\Actions\Jetstream\DeleteTeam;
use Liberu\Accounting\Erp\Actions\Jetstream\DeleteUser;
use Liberu\Accounting\Erp\Actions\Jetstream\InviteTeamMember;
use Liberu\Accounting\Erp\Actions\Jetstream\RemoveTeamMember;
use Liberu\Accounting\Erp\Actions\Jetstream\UpdateTeamName;
use Liberu\Accounting\Erp\Listeners\CreatePersonalTeam;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[\Override]
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePermissions();

        Jetstream::createTeamsUsing(CreateTeam::class);
        Jetstream::updateTeamNamesUsing(UpdateTeamName::class);
        Jetstream::addTeamMembersUsing(AddTeamMember::class);
        Jetstream::inviteTeamMembersUsing(InviteTeamMember::class);
        Jetstream::removeTeamMembersUsing(RemoveTeamMember::class);
        Jetstream::deleteTeamsUsing(DeleteTeam::class);
        Jetstream::deleteUsersUsing(DeleteUser::class);

        // Use our modified CreatePersonalTeam listener
        Event::listen(
            Registered::class,
            CreatePersonalTeam::class
        );
    }

    /**
     * Configure the roles and permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::role('admin', 'Administrator', [
            'create',
            'read',
            'update',
            'delete',
        ])->description('Administrator users can perform any action.');

        Jetstream::role('editor', 'Editor', [
            'read',
            'create',
            'update',
        ])->description('Editor users have the ability to read, create, and update.');
    }
}
