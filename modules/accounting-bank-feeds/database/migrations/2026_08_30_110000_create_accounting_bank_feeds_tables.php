<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_bank_feed_institutions', function (Blueprint $table): void {
            $table->id();
            $table->string('provider', 80);
            $table->string('external_id', 180);
            $table->string('name');
            $table->string('country', 2)->nullable();
            $table->string('logo_url')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['provider', 'external_id']);
        });
        Schema::create('accounting_bank_feed_connections', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->foreignId('institution_id')->constrained('accounting_bank_feed_institutions')->cascadeOnDelete();
            $table->string('provider', 80);
            $table->string('name');
            $table->string('external_reference', 180);
            $table->text('access_token');
            $table->text('credentials')->nullable();
            $table->text('cursor')->nullable();
            $table->string('status', 30)->default('active')->index();
            $table->timestamp('last_synced_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamp('last_error_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['provider', 'external_reference']);
        });
        Schema::create('accounting_bank_feed_account_mappings', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->foreignId('connection_id')->constrained('accounting_bank_feed_connections')->cascadeOnDelete();
            $table->unsignedBigInteger('bank_account_id')->index();
            $table->string('external_account_id', 180);
            $table->string('name');
            $table->string('currency', 3);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['connection_id', 'external_account_id']);
        });
        Schema::create('accounting_bank_feed_transactions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->foreignId('connection_id')->constrained('accounting_bank_feed_connections')->cascadeOnDelete();
            $table->foreignId('mapping_id')->nullable()->constrained('accounting_bank_feed_account_mappings')->nullOnDelete();
            $table->string('external_id', 220);
            $table->date('transaction_date');
            $table->date('posted_date')->nullable();
            $table->text('description')->nullable();
            $table->decimal('amount', 20, 6);
            $table->string('currency', 3);
            $table->string('status', 30)->default('posted')->index();
            $table->string('category')->nullable();
            $table->text('raw_data')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['connection_id', 'external_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_bank_feed_transactions');
        Schema::dropIfExists('accounting_bank_feed_account_mappings');
        Schema::dropIfExists('accounting_bank_feed_connections');
        Schema::dropIfExists('accounting_bank_feed_institutions');
    }
};
