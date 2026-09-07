<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomReportBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class CustomReport extends Model
{
    protected $table = 'accounting_custom_reports';

    protected $fillable = ['team_id', 'report_ref', 'name', 'measures', 'dimensions', 'filters', 'grouping', 'formulas', 'comparisons', 'layout', 'permissions'];

    protected $casts = ['measures' => 'array', 'dimensions' => 'array', 'filters' => 'array', 'grouping' => 'array', 'formulas' => 'array', 'comparisons' => 'array', 'layout' => 'array', 'permissions' => 'array'];

    public function variants(): HasMany
    {
        return $this->hasMany(CustomReportVariant::class, 'report_id');
    }

    public function exports(): HasMany
    {
        return $this->hasMany(CustomReportExport::class, 'report_id');
    }
}
