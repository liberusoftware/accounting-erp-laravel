<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReceiptManagement\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $id @property int|null $receipt_id @property string $requestee_ref @property string $target_type @property string $target_id @property string $status @property string|null $due_on */
final class MissingReceiptRequest extends Model
{
    protected $table = 'accounting_missing_receipt_requests';

    protected $fillable = ['team_id', 'receipt_id', 'requestee_ref', 'target_type', 'target_id', 'reason', 'status', 'due_on', 'fulfilled_at', 'metadata'];

    protected $casts = ['due_on' => 'date', 'fulfilled_at' => 'datetime', 'metadata' => 'array'];
}
