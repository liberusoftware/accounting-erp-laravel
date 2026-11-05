<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // PermissionsSeeder::class,
            MenuSeeder::class,
            TeamSeeder::class,
            RolesSeeder::class,
            UserSeeder::class,
        ]);
    }
}
