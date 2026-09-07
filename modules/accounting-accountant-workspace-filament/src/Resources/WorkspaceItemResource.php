<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountantWorkspaceFilament\Resources;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\AccountantWorkspace\Models\WorkspaceItem;

final class WorkspaceItemResource extends Resource
{
    protected static ?string $model = WorkspaceItem::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    protected static ?string $navigationLabel = 'Accountant Workspace';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required(), TextInput::make('subject_type'), TextInput::make('subject_id'), DateTimePicker::make('next_deadline')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('next_deadline')->dateTime(), TextColumn::make('subject_type')]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListWorkspaceItems::route('/'), 'create' => Pages\CreateWorkspaceItem::route('/create')];
    }
}
