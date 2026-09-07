<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiCurrency\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\MultiCurrency\Enums\CurrencyRole;

final class CurrencyProfile extends Model
{
    protected $table = 'accounting_multi_currency_profiles';

    protected $fillable = ['team_id', 'scope_ref', 'currency', 'role', 'is_active', 'metadata'];

    protected $casts = ['role' => CurrencyRole::class, 'is_active' => 'boolean', 'metadata' => 'array'];
}
