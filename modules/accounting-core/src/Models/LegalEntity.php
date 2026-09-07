<?php

declare(strict_types=1);

namespace Liberu\Accounting\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegalEntity extends Model
{
    protected $table = 'accounting_legal_entities';

    protected $fillable = ['name', 'registration_number', 'currency_code', 'accounting_basis', 'is_active'];

    protected $casts = ['is_active' => 'bool'];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
