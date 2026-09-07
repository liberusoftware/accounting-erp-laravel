<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollIntegrationFilament\Resources;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\PayrollIntegration\Actions\MarkPayrollImport;
use Liberu\Accounting\PayrollIntegration\Enums\ImportStatus;
use Liberu\Accounting\PayrollIntegration\Models\PayrollImport;
use Liberu\Accounting\PayrollIntegrationFilament\Resources\PayrollImportResource\Pages\ListPayrollImports;

final class PayrollImportResource extends Resource
{
    protected static ?string $model = PayrollImport::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static string|\UnitEnum|null $navigationGroup = 'Payroll';

    protected static ?string $navigationLabel = 'Payroll imports';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('provider')->searchable()->sortable(),
            TextColumn::make('run_ref')->label('Run')->searchable(),
            TextColumn::make('period_start')->date(),
            TextColumn::make('period_end')->date(),
            TextColumn::make('currency')->badge(),
            TextColumn::make('status')->badge(),
            TextColumn::make('imported_at')->dateTime(),
        ])->recordActions([
            Action::make('status')->label('Change status')->icon('heroicon-o-arrow-path')
                ->schema([Select::make('status')->options(collect(ImportStatus::cases())->mapWithKeys(fn (ImportStatus $status): array => [$status->value => ucfirst($status->value)])->all())->required()])
                ->action(fn (PayrollImport $record, array $data, MarkPayrollImport $action): PayrollImport => $action->handle($record, ImportStatus::from((string) $data['status']))),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => ListPayrollImports::route('/')];
    }
}
