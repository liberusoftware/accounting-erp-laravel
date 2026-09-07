<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReceiptManagement\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $id @property int $receipt_id @property string $action @property string|null $actor_ref @property array<string,mixed>|null $evidence */
final class ReceiptAudit extends Model
{
    protected $table = 'accounting_receipt_audits';

    protected $fillable = ['receipt_id', 'action', 'actor_ref', 'evidence', 'created_at'];

    public $timestamps = false;

    protected $casts = ['evidence' => 'array', 'created_at' => 'datetime'];
}
