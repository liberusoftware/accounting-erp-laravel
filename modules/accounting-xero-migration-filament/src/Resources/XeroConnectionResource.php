<?php

declare(strict_types=1);

namespace Liberu\Accounting\XeroMigrationFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\XeroMigration\Models\XeroConnection;

final class XeroConnectionResource extends Resource
{
    protected static ?string $model = XeroConnection::class;

    protected static ?string $navigationLabel = 'Xero connections';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('tenant_ref')->required(), TextInput::make('access_token')->password()->required(), TextInput::make('refresh_token')->password()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('tenant_ref')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('last_synced_at')->dateTime(), TextColumn::make('created_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListXeroConnections::route('/')];
    }
}
