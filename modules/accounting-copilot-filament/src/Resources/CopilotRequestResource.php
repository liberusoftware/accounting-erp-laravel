<?php

declare(strict_types=1);

namespace Liberu\Accounting\CopilotFilament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\Copilot\Models\CopilotRequest;
use Liberu\Accounting\CopilotFilament\Resources\CopilotRequestResource\Pages\ListCopilotRequests;

final class CopilotRequestResource extends Resource
{
    protected static ?string $model = CopilotRequest::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    protected static ?string $navigationLabel = 'Copilot Requests';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([Select::make('kind')->options(['search' => 'Search', 'explanation' => 'Explanation', 'summary' => 'Summary', 'narrative' => 'Narrative', 'draft_transaction' => 'Draft transaction'])->required(), Textarea::make('prompt')->required(), TextInput::make('confirmation_key')->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('kind')->badge(), TextColumn::make('prompt')->limit(60), TextColumn::make('status')->badge(), TextColumn::make('created_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    public static function getPages(): array
    {
        return ['index' => ListCopilotRequests::route('/')];
    }
}
