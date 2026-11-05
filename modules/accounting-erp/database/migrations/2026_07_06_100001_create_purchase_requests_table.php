<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_requests', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('supplier_id')->nullable()->constrained('suppliers', 'supplier_id')->nullOnDelete();
            $t->string('request_number')->unique();
            $t->date('request_date');
            $t->decimal('total_amount', 15, 2)->default(0);
            $t->string('status')->default('draft');
            $t->text('notes')->nullable();
            $t->string('approval_status')->default('pending');
            $t->foreignId('approved_by')->nullable();
            $t->timestamp('approved_at')->nullable();
            $t->text('rejection_reason')->nullable();
            $t->foreignId('team_id')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};
