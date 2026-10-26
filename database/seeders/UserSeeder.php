<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $adminPassword = (string) (config('app.seed_admin_password') ?: Str::random(64));
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make($adminPassword),
                'email_verified_at' => now(),
            ],
        );

        $team = Team::firstOrFail();
        $adminUser->forceFill(['current_team_id' => $team->id])->save();
        $adminUser->teams()->syncWithoutDetaching([$team->id]);

        setPermissionsTeamId($team->id);
        $role = Role::where('name', 'super_admin')->firstOrFail();
        $adminUser->assignRole($role);

        // Print passwords to console
        if (app()->environment('local') && $adminUser->wasRecentlyCreated && blank(config('app.seed_admin_password'))) {
            $this->command?->info("Admin password: {$adminPassword}");
        }
    }
}
