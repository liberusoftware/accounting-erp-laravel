<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_customer_portal_records', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('customer_id', 160);
            $table->string('type', 40);
            $table->string('reference', 160);
            $table->string('status', 40)->default('draft');
            $table->string('currency', 3)->nullable();
            $table->decimal('amount', 20, 8)->default(0);
            $table->json('payload')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'type', 'reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_customer_portal_records');
    }
};
