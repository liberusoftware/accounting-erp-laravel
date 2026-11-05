<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('bills', function (Blueprint $table): void {
            $table->boolean('is_recurring')->default(false)->after('notes');
            $table->string('recurrence_frequency')->nullable()->after('is_recurring');
            $table->date('recurrence_start')->nullable()->after('recurrence_frequency');
            $table->date('recurrence_end')->nullable()->after('recurrence_start');
            $table->date('last_generated')->nullable()->after('recurrence_end');
        });
    }

    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table): void {
            $table->dropColumn([
                'is_recurring',
                'recurrence_frequency',
                'recurrence_start',
                'recurrence_end',
                'last_generated',
            ]);
        });
    }
};
