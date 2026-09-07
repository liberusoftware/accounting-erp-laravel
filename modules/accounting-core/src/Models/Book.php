<?php

declare(strict_types=1);

namespace Liberu\Accounting\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $table = 'accounting_books';

    protected $fillable = ['legal_entity_id', 'name', 'code', 'accounting_basis', 'is_active'];

    protected $casts = ['is_active' => 'bool'];

    public function legalEntity(): BelongsTo
    {
        return $this->belongsTo(LegalEntity::class);
    }

    public function fiscalCalendars(): HasMany
    {
        return $this->hasMany(FiscalCalendar::class);
    }

    public function numberingSequences(): HasMany
    {
        return $this->hasMany(NumberingSequence::class);
    }

    public function defaults(): HasMany
    {
        return $this->hasMany(AccountingDefault::class);
    }

    public function policies(): HasMany
    {
        return $this->hasMany(AccountingPolicy::class);
    }
}
