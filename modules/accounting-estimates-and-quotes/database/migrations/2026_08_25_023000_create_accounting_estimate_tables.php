<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_sales_estimates', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('legal_entity_id')->index();
            $t->string('customer_ref');
            $t->string('quote_ref');
            $t->string('name');
            $t->char('currency', 3);
            $t->string('status')->index();
            $t->date('issue_date');
            $t->date('expires_on')->nullable();
            $t->unsignedInteger('version')->default(1);
            $t->text('terms')->nullable();
            $t->json('brand')->nullable();
            $t->timestamp('accepted_at')->nullable();
            $t->text('declined_reason')->nullable();
            $t->string('converted_ref')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['legal_entity_id', 'quote_ref']);
        });
        Schema::create('accounting_sales_estimate_items', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('estimate_id')->constrained('accounting_sales_estimates')->cascadeOnDelete();
            $t->string('item_ref')->nullable();
            $t->text('description');
            $t->decimal('quantity', 18, 4);
            $t->decimal('unit_price', 18, 2);
            $t->decimal('tax_rate', 8, 4)->default(0);
            $t->decimal('amount', 18, 2);
            $t->json('metadata')->nullable();
            $t->timestamps();
        });
        Schema::create('accounting_sales_estimate_versions', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('estimate_id')->constrained('accounting_sales_estimates')->cascadeOnDelete();
            $t->unsignedInteger('version');
            $t->json('snapshot');
            $t->string('created_by')->nullable();
            $t->timestamps();
            $t->unique(['estimate_id', 'version']);
        });
        Schema::create('accounting_sales_estimate_history', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('estimate_id')->constrained('accounting_sales_estimates')->cascadeOnDelete();
            $t->string('event');
            $t->string('actor_ref')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_sales_estimate_history');
        Schema::dropIfExists('accounting_sales_estimate_versions');
        Schema::dropIfExists('accounting_sales_estimate_items');
        Schema::dropIfExists('accounting_sales_estimates');
    }
};
