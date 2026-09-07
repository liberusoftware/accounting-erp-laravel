<?php

declare(strict_types=1);

namespace Liberu\Accounting\WorkpapersFilament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\Workpapers\Models\Workpaper;

final class WorkpaperResource extends Resource
{
    protected static ?string $model = Workpaper::class;

    protected static ?string $navigationLabel = 'Workpapers';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(160),
            TextInput::make('reference')->maxLength(80),
            DatePicker::make('period_start'),
            DatePicker::make('period_end'),
            TextInput::make('preparer_id')->numeric(),
            TextInput::make('reviewer_id')->numeric(),
            Textarea::make('conclusion')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable(),
            TextColumn::make('reference')->searchable(),
            TextColumn::make('status')->badge(),
            TextColumn::make('period_end')->date(),
            TextColumn::make('reviewer_id'),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListWorkpapers::route('/')];
    }
}
