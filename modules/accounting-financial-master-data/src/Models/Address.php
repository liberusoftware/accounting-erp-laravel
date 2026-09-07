<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterData\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $table = 'accounting_master_addresses';

    protected $fillable = ['party_id', 'kind', 'line_one', 'line_two', 'city', 'region', 'postal_code', 'country_code', 'is_primary', 'metadata'];

    protected $attributes = ['is_primary' => false];

    protected $casts = ['is_primary' => 'bool', 'metadata' => 'array'];

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }
}
