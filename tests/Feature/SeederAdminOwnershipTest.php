<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Erp\Database\Seeders\RolesSeeder;
use Liberu\Accounting\Erp\Database\Seeders\TeamSeeder;
use Liberu\Accounting\Erp\Database\Seeders\UserSeeder;
use Liberu\Accounting\Erp\Models\User;
use Liberu\Foundation\Organizations\Models\Team;

use function Pest\Laravel\seed;

uses(RefreshDatabase::class);

it('makes admin@example.com the owner of the Default team', function () {
    seed(TeamSeeder::class);
    seed(RolesSeeder::class);
    seed(UserSeeder::class);

    $admin = User::where('email', 'admin@example.com')->firstOrFail();
    $team = Team::where('name', 'Default')->firstOrFail();

    expect($team->user_id)->toBe($admin->id);
    expect($admin->current_team_id)->toBe($team->id);
});

it('leaves no throwaway owner@example.com placeholder user', function () {
    seed(TeamSeeder::class);
    seed(RolesSeeder::class);
    seed(UserSeeder::class);

    expect(User::where('email', 'owner@example.com')->exists())->toBeFalse();
});
