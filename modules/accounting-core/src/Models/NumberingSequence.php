<?php

declare(strict_types=1);

namespace Liberu\Accounting\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NumberingSequence extends Model
{
    protected $table = 'accounting_numbering_sequences';

    protected $fillable = ['book_id', 'key', 'prefix', 'next_number', 'padding'];

    protected $casts = ['next_number' => 'integer', 'padding' => 'integer'];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
