<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class PremiumCatalog extends Model
{
    protected $table = 'premium_catalogs';

    protected $fillable = ['catalog_key', 'stripe_product_id', 'stripe_monthly_price_id', 'stripe_yearly_price_id'];
}
