<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_spreadsheet_migration_templates', function (Blueprint $t): void {
            $t->id();
            $t->string('name');
            $t->string('entity', 80);
            $t->json('mapping');
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['name', 'entity']);
        });
        Schema::create('accounting_spreadsheet_migration_runs', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('template_id')->constrained('accounting_spreadsheet_migration_templates');
            $t->string('mode', 24);
            $t->string('status', 24)->default('draft');
            $t->char('source_hash', 64);
            $t->unsignedInteger('row_count')->default(0);
            $t->decimal('source_total', 20, 2)->default(0);
            $t->decimal('target_total', 20, 2)->default(0);
            $t->json('errors')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['template_id', 'source_hash']);
            $t->index(['status', 'mode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_spreadsheet_migration_runs');
        Schema::dropIfExists('accounting_spreadsheet_migration_templates');
    }
};
