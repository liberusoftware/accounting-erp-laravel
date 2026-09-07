<?php

declare(strict_types=1);

namespace Liberu\Accounting\Depreciation\Models;

use Illuminate\Database\Eloquent\Model;

final class DepreciationTaxBook extends Model
{
    protected $table = 'accounting_depreciation_tax_books';

    protected $fillable = ['team_id', 'book_ref', 'name', 'jurisdiction', 'metadata'];

    protected $casts = ['metadata' => 'array'];
}
