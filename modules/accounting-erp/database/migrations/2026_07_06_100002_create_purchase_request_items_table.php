<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_request_items', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('purchase_request_id')->constrained('purchase_requests')->cascadeOnDelete();
            $t->string('description')->nullable();
            $t->integer('quantity')->default(1);
            $t->decimal('unit_price', 15, 2)->default(0);
            $t->decimal('total_price', 15, 2)->default(0);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_request_items');
    }
};
