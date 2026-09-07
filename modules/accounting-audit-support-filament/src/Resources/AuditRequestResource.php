<?php

declare(strict_types=1);

namespace Liberu\Accounting\AuditSupportFilament\Resources;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\AuditSupport\Models\AuditRequest;

final class AuditRequestResource extends Resource
{
    protected static ?string $model = AuditRequest::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Tax & Compliance';

    protected static ?string $navigationLabel = 'Audit Support';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('reference'), TextInput::make('title')->required(), Textarea::make('description'), TextInput::make('owner_id')->numeric(), DateTimePicker::make('due_at')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('reference')->searchable(), TextColumn::make('title')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('due_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListAuditRequests::route('/'), 'create' => Pages\CreateAuditRequest::route('/create')];
    }
}
