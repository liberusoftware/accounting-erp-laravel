<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_insight_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('metric', 100);
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('value', 20, 4);
            $table->decimal('comparison_value', 20, 4)->nullable();
            $table->string('unit', 30)->nullable();
            $table->text('explanation')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('refreshed_at')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'metric', 'period_start', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_insight_snapshots');
    }
};
