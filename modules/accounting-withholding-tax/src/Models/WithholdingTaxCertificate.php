<?php

declare(strict_types=1);

namespace Liberu\Accounting\WithholdingTax\Models;

use Illuminate\Database\Eloquent\Model;

final class WithholdingTaxCertificate extends Model
{
    protected $table = 'accounting_withholding_tax_certificates';

    protected $fillable = ['team_id', 'rule_id', 'party_type', 'party_id', 'certificate_ref', 'valid_from', 'valid_until', 'status', 'metadata'];

    protected $casts = ['valid_from' => 'date', 'valid_until' => 'date', 'metadata' => 'array'];
}
