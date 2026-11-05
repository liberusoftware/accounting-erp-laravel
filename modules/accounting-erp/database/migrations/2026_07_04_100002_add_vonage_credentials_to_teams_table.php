<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Team-level Vonage (SMS) sending account. Encrypted at rest via the model cast;
// the columns are plain text so the ciphertext fits (encrypted values are long).
return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table): void {
            $table->text('vonage_key')->nullable()->after('personal_team');
            $table->text('vonage_secret')->nullable()->after('vonage_key');
            $table->string('vonage_from')->nullable()->after('vonage_secret');
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table): void {
            $table->dropColumn(['vonage_key', 'vonage_secret', 'vonage_from']);
        });
    }
};
