<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankFeedsFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\BankFeeds\Models\BankFeedConnection;

final class BankFeedConnectionResource extends Resource
{
    protected static ?string $model = BankFeedConnection::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    protected static ?string $navigationLabel = 'Bank Feeds';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required(), TextInput::make('provider')->required(), TextInput::make('external_reference')->required(), TextInput::make('institution_id')->numeric()->required(), TextInput::make('access_token')->password()->required()->dehydrated()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('provider')->badge(), TextColumn::make('institution.name'), TextColumn::make('status')->badge(), TextColumn::make('last_synced_at')->dateTime()])->defaultSort('name');
    }
}
