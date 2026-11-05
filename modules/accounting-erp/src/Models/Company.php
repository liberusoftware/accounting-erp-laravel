<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Models;

use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\Erp\Database\Factories\CompanyFactory;
use Liberu\Accounting\Erp\Traits\IsTenantModel;

/**
 * @property int $company_id
 * @property string|null $company_name
 * @property string|null $company_email
 * @property string|null $hmrc_paye_reference
 * @property string|null $hmrc_corporation_tax_utr
 * @property string|null $hmrc_vat_number
 */
#[UseFactory(CompanyFactory::class)]
class Company extends Model
{
    use HasFactory;
    use IsTenantModel;

    #[\Override]
    protected $primaryKey = 'company_id';

    #[\Override]
    protected $fillable = [
        'company_name',
        'company_address',
        'company_email',
        'company_phone',
        'company_city',
        'company_tin',
        'company_logo',
        'hmrc_utr',
        'hmrc_paye_reference',
        'hmrc_vat_number',
        'hmrc_accounts_office_reference',
        'hmrc_corporation_tax_utr',
        'vat_scheme',
        'vat_period',
    ];

    /**
     * Get HMRC submissions for this company.
     */
    public function hmrcSubmissions()
    {
        return $this->hasMany(HmrcSubmission::class);
    }

    /**
     * Get VAT returns for this company.
     */
    public function hmrcVatReturns()
    {
        return $this->hasMany(HmrcVatReturn::class);
    }

    /**
     * Get PAYE submissions for this company.
     */
    public function hmrcPayeSubmissions()
    {
        return $this->hasMany(HmrcPayeSubmission::class);
    }

    /**
     * Get corporation tax submissions for this company.
     */
    public function hmrcCorporationTaxSubmissions()
    {
        return $this->hasMany(HmrcCorporationTaxSubmission::class);
    }

    /**
     * Check if company is registered for VAT.
     */
    public function isVatRegistered(): bool
    {
        return ! empty($this->hmrc_vat_number);
    }

    /**
     * Check if company has PAYE scheme.
     */
    public function hasPayeScheme(): bool
    {
        return ! empty($this->hmrc_paye_reference);
    }

    /**
     * Check if company is registered for Corporation Tax.
     */
    public function isCorporationTaxRegistered(): bool
    {
        return ! empty($this->hmrc_corporation_tax_utr);
    }
}
