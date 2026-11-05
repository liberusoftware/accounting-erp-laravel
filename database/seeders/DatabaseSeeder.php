<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Liberu\Accounting\Erp\Database\Seeders\DatabaseSeeder as ErpDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ErpDatabaseSeeder::class);
    }
}
