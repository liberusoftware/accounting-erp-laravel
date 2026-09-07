<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankFeeds\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class BankFeedAccountMapping extends Model
{
    protected $table = 'accounting_bank_feed_account_mappings';

    protected $fillable = ['team_id', 'connection_id', 'bank_account_id', 'external_account_id', 'name', 'currency', 'metadata'];

    protected $casts = ['metadata' => 'array'];

    public function connection(): BelongsTo
    {
        return $this->belongsTo(BankFeedConnection::class, 'connection_id');
    }
}
