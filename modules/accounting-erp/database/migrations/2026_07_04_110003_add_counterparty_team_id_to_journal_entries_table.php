<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tags a journal entry as intercompany: the other member team it transacts with.
// Consolidated statements net out amounts on entries whose counterparty is a
// fellow group member, so the group isn't double-counted.
return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('journal_entries', function (Blueprint $table): void {
            $table->foreignId('counterparty_team_id')->nullable()->after('entry_type')
                ->constrained('teams')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('counterparty_team_id');
        });
    }
};
