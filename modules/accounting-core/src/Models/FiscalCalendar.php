<?php

declare(strict_types=1);

namespace Liberu\Accounting\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FiscalCalendar extends Model
{
    protected $table = 'accounting_fiscal_calendars';

    protected $fillable = ['book_id', 'starts_on', 'ends_on', 'is_closed'];

    protected $casts = ['starts_on' => 'date', 'ends_on' => 'date', 'is_closed' => 'bool'];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
