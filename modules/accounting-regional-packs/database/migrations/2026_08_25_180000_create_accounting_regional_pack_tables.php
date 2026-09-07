<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_regional_packs', function (Blueprint $t): void {
            $t->id();
            $t->char('country_code', 2);
            $t->string('locale', 20);
            $t->char('currency', 3);
            $t->string('version', 40);
            $t->string('status', 24)->default('draft');
            $t->date('effective_from');
            $t->date('effective_to')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['country_code', 'version']);
        });
        Schema::create('accounting_regional_pack_artifacts', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('pack_id')->constrained('accounting_regional_packs')->cascadeOnDelete();
            $t->string('type', 32);
            $t->string('key', 190);
            $t->json('definition');
            $t->string('status', 24)->default('draft');
            $t->json('test_results')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['pack_id', 'type', 'key']);
            $t->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_regional_pack_artifacts');
        Schema::dropIfExists('accounting_regional_packs');
    }
};
