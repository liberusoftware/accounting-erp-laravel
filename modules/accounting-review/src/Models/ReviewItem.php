<?php

declare(strict_types=1);

namespace Liberu\Accounting\Review\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\Review\Enums\ReviewStatus;

final class ReviewItem extends Model
{
    protected $table = 'accounting_review_items';

    protected $fillable = ['team_id', 'item_type', 'source_type', 'source_id', 'severity', 'status', 'title', 'details', 'resolution', 'resolved_by', 'resolved_at', 'signoff', 'signed_off_by', 'signed_off_at', 'due_at'];

    protected $casts = ['status' => ReviewStatus::class, 'details' => 'array', 'resolution' => 'array', 'signoff' => 'array', 'resolved_by' => 'integer', 'signed_off_by' => 'integer', 'resolved_at' => 'datetime', 'signed_off_at' => 'datetime', 'due_at' => 'datetime'];
}
