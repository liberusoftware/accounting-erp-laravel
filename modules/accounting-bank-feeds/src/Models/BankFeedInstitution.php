<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankFeeds\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class BankFeedInstitution extends Model
{
    protected $table = 'accounting_bank_feed_institutions';

    protected $fillable = ['provider', 'external_id', 'name', 'country', 'logo_url', 'metadata'];

    protected $casts = ['metadata' => 'array'];

    public function connections(): HasMany
    {
        return $this->hasMany(BankFeedConnection::class, 'institution_id');
    }
}
