<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_collaboration_threads', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('thread_ref', 160);
            $table->string('kind', 60);
            $table->string('subject', 200);
            $table->string('status', 40)->default('open');
            $table->json('participants')->nullable();
            $table->json('messages')->nullable();
            $table->json('approvals')->nullable();
            $table->json('reminders')->nullable();
            $table->json('evidence')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'thread_ref']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_collaboration_threads');
    }
};
