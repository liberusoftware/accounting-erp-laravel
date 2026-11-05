<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('premium_catalogs', function (Blueprint $table): void {
            $table->id();
            $table->string('catalog_key')->unique();
            $table->string('stripe_product_id')->unique();
            $table->string('stripe_monthly_price_id')->unique();
            $table->string('stripe_yearly_price_id')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('premium_catalogs');
    }
};
