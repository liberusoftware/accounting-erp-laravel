<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollJournalsFilament\Resources;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\PayrollJournals\Actions\PostPayrollJournal;
use Liberu\Accounting\PayrollJournals\Actions\ReversePayrollJournal;
use Liberu\Accounting\PayrollJournals\Models\PayrollJournal;
use Liberu\Accounting\PayrollJournalsFilament\Resources\PayrollJournalResource\Pages\ListPayrollJournals;

final class PayrollJournalResource extends Resource
{
    protected static ?string $model = PayrollJournal::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Payroll';

    protected static ?string $navigationLabel = 'Payroll journals';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('journal_ref')->label('Journal')->searchable()->sortable(),
            TextColumn::make('payroll_period_start')->date(),
            TextColumn::make('payroll_period_end')->date(),
            TextColumn::make('currency')->badge(),
            TextColumn::make('gross_pay')->money(fn (PayrollJournal $record): string => $record->currency),
            TextColumn::make('net_pay')->money(fn (PayrollJournal $record): string => $record->currency),
            TextColumn::make('status')->badge(),
        ])->recordActions([
            Action::make('post')->label('Post')->icon('heroicon-o-check')->visible(fn (PayrollJournal $record): bool => $record->status->value === 'draft')
                ->action(fn (PayrollJournal $record, PostPayrollJournal $action): PayrollJournal => $action->handle($record)),
            Action::make('reverse')->label('Reverse')->icon('heroicon-o-arrow-uturn-left')->visible(fn (PayrollJournal $record): bool => $record->status->value === 'posted')
                ->schema([TextInput::make('reversal_ref')->required()->maxLength(100)])
                ->action(fn (PayrollJournal $record, array $data, ReversePayrollJournal $action): PayrollJournal => $action->handle($record, (string) $data['reversal_ref'])),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => ListPayrollJournals::route('/')];
    }
}
