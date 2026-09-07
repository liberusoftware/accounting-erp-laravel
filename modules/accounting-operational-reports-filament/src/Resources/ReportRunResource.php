<?php

declare(strict_types=1);

namespace Liberu\Accounting\OperationalReportsFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Liberu\Accounting\OperationalReports\Models\ReportRun;

final class ReportRunResource extends Resource
{
    protected static ?string $model = ReportRun::class;

    protected static ?string $navigationLabel = 'Operational Reports';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('report_key')->required(), TextInput::make('name')->required(), TextInput::make('category')->required(), TextInput::make('period_start')->required(), TextInput::make('period_end')->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('report_key')->searchable(), TextColumn::make('name')->searchable(), TextColumn::make('category')->badge(), TextColumn::make('status')->badge(), TextColumn::make('summary.row_count')->label('Rows'), TextColumn::make('exceptions_count')->counts('exceptions'), TextColumn::make('period_end')->date()->sortable()])->filters([SelectFilter::make('category')->options(['receivables_payables' => 'Receivables/Payables', 'sales_purchases' => 'Sales/Purchases', 'tax' => 'Tax', 'bank' => 'Bank', 'inventory' => 'Inventory', 'assets' => 'Assets', 'expenses' => 'Expenses', 'projects' => 'Projects', 'payroll' => 'Payroll', 'exceptions' => 'Exceptions']), SelectFilter::make('status')->options(['ready' => 'Ready', 'published' => 'Published', 'failed' => 'Failed'])])->defaultSort('period_end', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListReportRuns::route('/')];
    }
}
