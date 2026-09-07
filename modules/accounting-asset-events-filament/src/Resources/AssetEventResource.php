<?php

declare(strict_types=1);

namespace Liberu\Accounting\AssetEventsFilament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\AssetEvents\Models\AssetEvent;

final class AssetEventResource extends Resource
{
    protected static ?string $model = AssetEvent::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static string|\UnitEnum|null $navigationGroup = 'Assets';

    protected static ?string $navigationLabel = 'Asset Events';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('asset_id')->numeric()->required(), TextInput::make('event_type')->required(), DatePicker::make('event_date')->required(), TextInput::make('description')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('asset_id')->sortable(), TextColumn::make('event_type')->badge(), TextColumn::make('event_date')->date(), TextColumn::make('description')->searchable(), TextColumn::make('actor_id')]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListAssetEvents::route('/'), 'create' => Pages\CreateAssetEvent::route('/create')];
    }
}
