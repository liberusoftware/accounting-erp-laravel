<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('revenue_schedule_entries', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('revenue_schedule_id')->constrained()->cascadeOnDelete();
            $t->unsignedInteger('period_number');
            $t->date('recognition_date');
            $t->decimal('amount', 15, 2)->default(0);
            $t->boolean('recognized')->default(false);
            $t->timestamp('recognized_at')->nullable();
            $t->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $t->timestamps();
            $t->unique(['revenue_schedule_id', 'period_number'], 'rev_sched_entry_period_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revenue_schedule_entries');
    }
};
