<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollLiabilitiesFilament\Resources;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\PayrollLiabilities\Actions\AllocatePayrollLiability;
use Liberu\Accounting\PayrollLiabilities\Enums\LiabilityStatus;
use Liberu\Accounting\PayrollLiabilities\Models\PayrollLiability;
use Liberu\Accounting\PayrollLiabilitiesFilament\Resources\PayrollLiabilityResource\Pages\ListPayrollLiabilities;

final class PayrollLiabilityResource extends Resource
{
    protected static ?string $model = PayrollLiability::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-scale';

    protected static string|\UnitEnum|null $navigationGroup = 'Payroll';

    protected static ?string $navigationLabel = 'Payroll liabilities';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('liability_ref')->label('Liability')->searchable()->sortable(),
            TextColumn::make('agency_ref')->placeholder('—'),
            TextColumn::make('payee_ref')->placeholder('—'),
            TextColumn::make('due_on')->date()->sortable(),
            TextColumn::make('amount')->money(fn (PayrollLiability $record): string => $record->currency),
            TextColumn::make('paid_amount')->money(fn (PayrollLiability $record): string => $record->currency),
            TextColumn::make('status')->badge(),
        ])->recordActions([
            Action::make('allocate')->label('Allocate payment')->icon('heroicon-o-banknotes')
                ->visible(fn (PayrollLiability $record): bool => $record->outstanding() > 0 && $record->status !== LiabilityStatus::Reconciled)
                ->schema([
                    TextInput::make('amount')->numeric()->rule('gt:0')->required(),
                    TextInput::make('allocation_ref')->required()->maxLength(255),
                ])
                ->action(fn (PayrollLiability $record, array $data, AllocatePayrollLiability $action): PayrollLiability => $action->handle($record, (float) $data['amount'], (string) $data['allocation_ref'])),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => ListPayrollLiabilities::route('/')];
    }
}
