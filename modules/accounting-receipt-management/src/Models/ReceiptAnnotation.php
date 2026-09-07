<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReceiptManagement\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $id @property int $receipt_id @property string $author_ref @property string $body @property string $visibility */
final class ReceiptAnnotation extends Model
{
    protected $table = 'accounting_receipt_annotations';

    protected $fillable = ['receipt_id', 'author_ref', 'body', 'visibility', 'metadata'];

    protected $casts = ['metadata' => 'array'];
}
