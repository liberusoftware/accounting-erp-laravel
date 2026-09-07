<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankFeeds\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\BankFeeds\Enums\ConnectionStatus;

final class BankFeedConnection extends Model
{
    protected $table = 'accounting_bank_feed_connections';

    protected $fillable = ['team_id', 'user_id', 'institution_id', 'provider', 'name', 'external_reference', 'access_token', 'credentials', 'cursor', 'status', 'last_synced_at', 'last_error', 'last_error_at', 'metadata'];

    protected $hidden = ['access_token', 'credentials', 'cursor'];

    protected $casts = ['access_token' => 'encrypted', 'credentials' => 'encrypted:array', 'status' => ConnectionStatus::class, 'last_synced_at' => 'datetime', 'last_error_at' => 'datetime', 'metadata' => 'array'];

    protected $attributes = ['status' => 'active'];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(BankFeedInstitution::class, 'institution_id');
    }

    public function mappings(): HasMany
    {
        return $this->hasMany(BankFeedAccountMapping::class, 'connection_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(BankFeedTransaction::class, 'connection_id');
    }
}
