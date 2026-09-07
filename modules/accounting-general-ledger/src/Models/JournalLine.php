<?php

declare(strict_types=1);

namespace Liberu\Accounting\GeneralLedger\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\ChartOfAccounts\Models\Account;

/**
 * @property int|string $account_id
 * @property string $debit
 * @property string $credit
 * @property string|null $description
 * @property array<string,mixed>|null $dimensions
 */
class JournalLine extends Model
{
    protected $table = 'accounting_journal_lines';

    protected $fillable = ['journal_entry_id', 'account_id', 'debit', 'credit', 'description', 'dimensions'];

    protected $casts = ['debit' => 'decimal:2', 'credit' => 'decimal:2', 'dimensions' => 'array'];

    protected static function booted(): void
    {
        static::saving(function (self $line): void {
            if ($line->exists && $line->journalEntry()->where('status', '!=', 'draft')->exists()) {
                throw new \LogicException('Lines of posted or reversed journals are immutable.');
            }
        });
        static::deleting(function (self $line): void {
            if ($line->journalEntry()->where('status', '!=', 'draft')->exists()) {
                throw new \LogicException('Lines of posted or reversed journals cannot be deleted.');
            }
        });
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    /** @return BelongsTo<Account, $this> */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
