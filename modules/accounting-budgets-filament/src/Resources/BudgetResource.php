<?php

declare(strict_types=1);

namespace Liberu\Accounting\BudgetsFilament\Resources;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\Budgets\Actions\ApproveBudget;
use Liberu\Accounting\Budgets\Actions\SubmitBudget;
use Liberu\Accounting\Budgets\Models\Budget;

final class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Budgets';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            DatePicker::make('period_start')->required(),
            DatePicker::make('period_end')->required(),
            TextInput::make('currency')->length(3)->default('GBP')->required(),
            TextInput::make('notes')->maxLength(1000),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('period_start')->date()->sortable(),
            TextColumn::make('period_end')->date(),
            TextColumn::make('currency'),
            TextColumn::make('status')->badge(),
            TextColumn::make('version'),
        ])->recordActions([
            Action::make('submit')->requiresConfirmation()->action(fn (Budget $record): Budget => app(SubmitBudget::class)->handle($record, (int) auth()->id()))->visible(fn (Budget $record): bool => $record->status->value === 'draft' || $record->status->value === 'revised'),
            Action::make('approve')->color('success')->requiresConfirmation()->action(fn (Budget $record): Budget => app(ApproveBudget::class)->handle($record, (int) auth()->id()))->visible(fn (Budget $record): bool => $record->status->value === 'submitted'),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1))->with('lines');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListBudgets::route('/'), 'create' => Pages\CreateBudget::route('/create')];
    }
}
