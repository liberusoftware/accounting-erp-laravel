<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_job_estimates', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('team_id')->nullable()->index();
            $t->string('estimate_ref');
            $t->string('project_ref')->index();
            $t->string('title');
            $t->char('currency', 3);
            $t->string('status')->index();
            $t->unsignedInteger('version_no')->default(1);
            $t->decimal('total_cost', 18, 2)->default(0);
            $t->decimal('total_revenue', 18, 2)->default(0);
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'estimate_ref']);
        });
        Schema::create('accounting_estimate_versions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('estimate_id')->constrained('accounting_job_estimates')->cascadeOnDelete();
            $t->unsignedInteger('version_no');
            $t->string('status')->index();
            $t->text('notes')->nullable();
            $t->decimal('total_cost', 18, 2)->default(0);
            $t->decimal('total_revenue', 18, 2)->default(0);
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['estimate_id', 'version_no']);
        });
        Schema::create('accounting_estimate_lines', function (Blueprint $t) {
            $t->id();
            $t->foreignId('estimate_id')->constrained('accounting_job_estimates')->cascadeOnDelete();
            $t->foreignId('version_id')->nullable()->constrained('accounting_estimate_versions')->nullOnDelete();
            $t->string('line_ref');
            $t->string('line_type');
            $t->string('category');
            $t->string('description');
            $t->decimal('quantity', 18, 4);
            $t->decimal('rate', 18, 4);
            $t->decimal('amount', 18, 2);
            $t->decimal('actual_amount', 18, 2)->default(0);
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['estimate_id', 'version_id']);
        });
        Schema::create('accounting_estimate_approvals', function (Blueprint $t) {
            $t->id();
            $t->foreignId('estimate_id')->constrained('accounting_job_estimates')->cascadeOnDelete();
            $t->foreignId('version_id')->nullable()->constrained('accounting_estimate_versions')->nullOnDelete();
            $t->string('actor_ref');
            $t->string('decision');
            $t->text('comment')->nullable();
            $t->timestamp('decided_at');
            $t->json('metadata')->nullable();
            $t->timestamps();
        });
        Schema::create('accounting_estimate_actuals', function (Blueprint $t) {
            $t->id();
            $t->foreignId('estimate_id')->constrained('accounting_job_estimates')->cascadeOnDelete();
            $t->foreignId('version_id')->nullable()->constrained('accounting_estimate_versions')->nullOnDelete();
            $t->string('line_ref')->nullable();
            $t->string('category');
            $t->decimal('amount', 18, 2);
            $t->string('source_ref');
            $t->timestamp('occurred_at');
            $t->json('metadata')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_estimate_actuals');
        Schema::dropIfExists('accounting_estimate_approvals');
        Schema::dropIfExists('accounting_estimate_lines');
        Schema::dropIfExists('accounting_estimate_versions');
        Schema::dropIfExists('accounting_job_estimates');
    }
};
