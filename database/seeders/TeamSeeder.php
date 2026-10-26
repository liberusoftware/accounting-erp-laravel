<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminPassword = (string) (config('app.seed_admin_password') ?: Str::random(64));

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make($adminPassword),
                'email_verified_at' => now(),
            ],
        );

        if (app()->environment('local') && $admin->wasRecentlyCreated && blank(config('app.seed_admin_password'))) {
            $this->command?->info("Admin password: {$adminPassword}");
        }

        $team = Team::firstOrCreate([
            'name' => 'Default',
            'personal_team' => false,
        ], ['user_id' => $admin->id]);

        $admin->forceFill(['current_team_id' => $team->id])->save();
        $admin->teams()->syncWithoutDetaching([$team->id]);
    }
}
