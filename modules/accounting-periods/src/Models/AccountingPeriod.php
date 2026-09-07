<?php

declare(strict_types=1);

namespace Liberu\Accounting\Periods\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\Core\Models\Book;
use Liberu\Accounting\Periods\Enums\PeriodState;

class AccountingPeriod extends Model
{
    protected $table = 'accounting_periods';

    protected $fillable = ['book_id', 'starts_on', 'ends_on', 'state', 'posting_ends_on', 'locked_by', 'locked_at', 'reopened_by', 'reopen_reason', 'evidence'];

    protected $casts = ['starts_on' => 'date', 'ends_on' => 'date', 'posting_ends_on' => 'date', 'locked_at' => 'datetime', 'evidence' => 'array', 'state' => PeriodState::class];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function isPostingAllowed(\DateTimeInterface|string $date): bool
    {
        $value = $date instanceof \DateTimeInterface ? $date->format('Y-m-d') : $date;

        return $this->state === PeriodState::Open && $value >= $this->starts_on->toDateString() && $value <= ($this->posting_ends_on?->toDateString() ?? $this->ends_on->toDateString());
    }
}
