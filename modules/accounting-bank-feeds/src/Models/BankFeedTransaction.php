<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankFeeds\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\BankFeeds\Enums\FeedTransactionStatus;

final class BankFeedTransaction extends Model
{
    protected $table = 'accounting_bank_feed_transactions';

    protected $fillable = ['team_id', 'connection_id', 'mapping_id', 'external_id', 'transaction_date', 'posted_date', 'description', 'amount', 'currency', 'status', 'category', 'raw_data', 'metadata'];

    protected $hidden = ['raw_data'];

    protected $casts = ['transaction_date' => 'date', 'posted_date' => 'date', 'amount' => 'decimal:2', 'status' => FeedTransactionStatus::class, 'raw_data' => 'encrypted:array', 'metadata' => 'array'];

    protected $attributes = ['status' => 'posted'];

    public function connection(): BelongsTo
    {
        return $this->belongsTo(BankFeedConnection::class, 'connection_id');
    }

    public function mapping(): BelongsTo
    {
        return $this->belongsTo(BankFeedAccountMapping::class, 'mapping_id');
    }
}
