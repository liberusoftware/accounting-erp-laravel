<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// The approval engine (#826) added rejection_reason to JournalEntry::$fillable
// and approve/reject via the Approvable trait, but never added the column, so
// markRejected() would hard-error on MySQL/prod (SchemaConsistencyTest caught it).
return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('journal_entries', function (Blueprint $table): void {
            if (! Schema::hasColumn('journal_entries', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('approved_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table): void {
            $table->dropColumn('rejection_reason');
        });
    }
};
