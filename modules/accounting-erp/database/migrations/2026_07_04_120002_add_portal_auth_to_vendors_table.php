<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Make Vendor authenticatable for the vendor portal. email is the auth username,
// so it gets a (nullable) unique index — duplicates would make login ambiguous.
return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table): void {
            $table->string('password')->nullable()->after('email');
            $table->rememberToken();
            $table->unique('email');
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table): void {
            $table->dropUnique(['email']);
            $table->dropColumn(['password', 'remember_token']);
        });
    }
};
