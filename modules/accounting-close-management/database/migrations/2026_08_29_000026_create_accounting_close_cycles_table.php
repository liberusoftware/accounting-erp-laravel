<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_close_cycles', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('cycle_ref', 160);
            $table->string('period', 40);
            $table->date('due_date');
            $table->string('status', 40)->default('open');
            $table->json('checklist')->nullable();
            $table->json('owners')->nullable();
            $table->json('dependencies')->nullable();
            $table->json('evidence')->nullable();
            $table->json('reconciliations')->nullable();
            $table->json('adjustments')->nullable();
            $table->json('review')->nullable();
            $table->json('certification')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'cycle_ref']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_close_cycles');
    }
};
