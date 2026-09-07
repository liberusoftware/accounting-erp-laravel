<?php

declare(strict_types=1);

namespace Liberu\Accounting\TransfersFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\Transfers\Models\Transfer;

final class TransferResource extends Resource
{
    protected static ?string $model = Transfer::class;

    protected static ?string $navigationLabel = 'Transfers';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('source_account_ref')->required(), TextInput::make('destination_account_ref')->required(), TextInput::make('source_currency')->required(), TextInput::make('destination_currency')->required(), TextInput::make('source_amount')->numeric()->required(), TextInput::make('exchange_rate')->numeric()->required(), TextInput::make('fee_amount')->numeric()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('source_account_ref'), TextColumn::make('destination_account_ref'), TextColumn::make('source_amount'), TextColumn::make('destination_amount'), TextColumn::make('status')->badge(), TextColumn::make('created_at')->dateTime()->sortable()]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListTransfers::route('/')];
    }
}
