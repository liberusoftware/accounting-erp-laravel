<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('consolidation_group_team', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('consolidation_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['consolidation_group_id', 'team_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consolidation_group_team');
    }
};
