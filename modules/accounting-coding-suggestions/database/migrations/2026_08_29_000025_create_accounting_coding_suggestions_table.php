<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_coding_suggestions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('source_ref', 160);
            $table->string('target_type', 60);
            $table->string('target_ref', 160);
            $table->decimal('confidence', 8, 6);
            $table->text('explanation');
            $table->string('status', 40)->default('pending');
            $table->json('feedback')->nullable();
            $table->json('policy')->nullable();
            $table->json('review')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'source_ref', 'target_type', 'target_ref']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_coding_suggestions');
    }
};
