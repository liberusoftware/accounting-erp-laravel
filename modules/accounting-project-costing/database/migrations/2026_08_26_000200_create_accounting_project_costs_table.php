<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_project_costs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->unsignedBigInteger('project_job_id')->index();
            $table->string('type');
            $table->date('occurred_on');
            $table->decimal('amount', 20, 2);
            $table->char('currency', 3)->default('GBP');
            $table->boolean('committed')->default(false);
            $table->boolean('actual')->default(true);
            $table->decimal('wip_amount', 20, 2)->default(0);
            $table->string('source_ref')->nullable();
            $table->json('dimensions')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['project_job_id', 'type', 'source_ref']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_project_costs');
    }
};
