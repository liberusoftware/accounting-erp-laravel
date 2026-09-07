<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_consolidation_groups', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('group_ref', 160);
            $table->string('name', 200);
            $table->string('reporting_currency', 3);
            $table->string('status', 40)->default('draft');
            $table->json('entities')->nullable();
            $table->json('eliminations')->nullable();
            $table->json('translation')->nullable();
            $table->json('report')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'group_ref']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_consolidation_groups');
    }
};
