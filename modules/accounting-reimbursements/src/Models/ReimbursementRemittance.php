<?php

declare(strict_types=1);

namespace Liberu\Accounting\Reimbursements\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $id @property int $batch_id @property string $payee_ref @property float|string $amount @property string $status @property string|null $document_ref */
final class ReimbursementRemittance extends Model
{
    protected $table = 'accounting_reimbursement_remittances';

    protected $fillable = ['batch_id', 'payee_ref', 'amount', 'currency', 'status', 'document_ref', 'sent_at', 'metadata'];

    protected $casts = ['amount' => 'decimal:2', 'sent_at' => 'datetime', 'metadata' => 'array'];
}
