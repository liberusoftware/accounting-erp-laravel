<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_review_items', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('item_type', 60)->index();
            $table->string('source_type', 120)->nullable();
            $table->string('source_id', 190)->nullable();
            $table->string('severity', 20)->default('medium')->index();
            $table->string('status', 20)->default('open')->index();
            $table->string('title');
            $table->json('details')->nullable();
            $table->json('resolution')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->json('signoff')->nullable();
            $table->unsignedBigInteger('signed_off_by')->nullable();
            $table->timestamp('signed_off_at')->nullable();
            $table->timestamp('due_at')->nullable()->index();
            $table->timestamps();
            $table->index(['team_id', 'source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_review_items');
    }
};
