<?php

return [
    'enabled' => filter_var(env('PREMIUM_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
    'card_required' => filter_var(env('PREMIUM_CARD_REQUIRED', true), FILTER_VALIDATE_BOOLEAN),
    'trial_days' => 14,
    'product_name' => env('STRIPE_PREMIUM_PRODUCT_NAME', 'Accounting ERP Premium'),
    'interval' => env('PREMIUM_BILLING_INTERVAL', 'month'),
    'monthly_amount' => 499,
    'yearly_amount' => 4999,
    'currency' => 'gbp',
    'secret' => env('STRIPE_SECRET'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
];
