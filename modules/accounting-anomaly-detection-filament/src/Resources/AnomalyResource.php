<?php

declare(strict_types=1);

namespace Liberu\Accounting\AnomalyDetectionFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\AnomalyDetection\Models\Anomaly;

final class AnomalyResource extends Resource
{
    protected static ?string $model = Anomaly::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    protected static ?string $navigationLabel = 'Anomaly Detection';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('kind')->required(), TextInput::make('title')->required(), TextInput::make('confidence')->numeric()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('kind')->badge(), TextColumn::make('title')->searchable(), TextColumn::make('confidence'), TextColumn::make('status')->badge(), TextColumn::make('detected_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListAnomalies::route('/'), 'create' => Pages\CreateAnomaly::route('/create')];
    }
}
