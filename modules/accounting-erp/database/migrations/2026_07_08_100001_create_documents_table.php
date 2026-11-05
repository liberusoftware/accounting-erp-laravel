<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $t): void {
            $t->id();
            $t->string('documentable_type');
            $t->unsignedBigInteger('documentable_id');
            $t->string('name');
            $t->string('disk')->default('local');
            $t->date('retention_until')->nullable();
            $t->foreignId('team_id')->nullable();
            $t->timestamps();
            $t->index(['documentable_type', 'documentable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
