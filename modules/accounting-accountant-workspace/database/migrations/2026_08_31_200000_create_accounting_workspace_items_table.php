<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_workspace_items', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('subject_type', 120)->nullable();
            $table->string('subject_id', 190)->nullable();
            $table->string('name');
            $table->string('status', 30)->default('active')->index();
            $table->timestamp('next_deadline')->nullable()->index();
            $table->json('alerts')->nullable();
            $table->json('notes')->nullable();
            $table->json('requests')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_workspace_items');
    }
};
