<?php

declare(strict_types=1);

namespace Liberu\Accounting\RecurringTransactionsFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\RecurringTransactions\Models\RecurringTemplate;

final class RecurringTemplateResource extends Resource
{
    protected static ?string $model = RecurringTemplate::class;

    protected static ?string $navigationLabel = 'Recurring transactions';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required(), TextInput::make('transaction_type')->required(), TextInput::make('frequency')->required(), TextInput::make('starts_on')->required(), TextInput::make('next_run_on')->required(), TextInput::make('ends_on'), TextInput::make('payload')->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('transaction_type'), TextColumn::make('frequency')->badge(), TextColumn::make('next_run_on')->date()->sortable(), TextColumn::make('status')->badge(), IconColumn::make('automatic')->boolean()])->defaultSort('next_run_on');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListRecurringTemplates::route('/')];
    }
}
