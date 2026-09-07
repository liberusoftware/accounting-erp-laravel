<?php

declare(strict_types=1);

namespace Liberu\Accounting\Policies\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\Core\Models\Book;
use Liberu\Accounting\Policies\Enums\PolicyCategory;

class PolicyRule extends Model
{
    protected $table = 'accounting_policy_rules';

    protected $fillable = ['book_id', 'category', 'key', 'value', 'effective_from', 'effective_until', 'is_active', 'approved_by', 'approved_at', 'metadata'];

    protected $casts = ['category' => PolicyCategory::class, 'value' => 'array', 'metadata' => 'array', 'effective_from' => 'date', 'effective_until' => 'date', 'approved_at' => 'datetime', 'is_active' => 'bool'];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function appliesOn(\DateTimeInterface|string $date): bool
    {
        $value = $date instanceof \DateTimeInterface ? $date->format('Y-m-d') : $date;

        return $this->is_active && $value >= $this->effective_from->toDateString() && ($this->effective_until === null || $value <= $this->effective_until->toDateString());
    }

    public function isApproved(): bool
    {
        return $this->approved_by !== null && $this->approved_at !== null;
    }
}
