<?php

declare(strict_types=1);

namespace Liberu\Accounting\MatchingIntelligenceFilament\Resources;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\MatchingIntelligence\Models\MatchingSuggestion;

final class MatchingSuggestionResource extends Resource
{
    protected static ?string $model = MatchingSuggestion::class;

    protected static ?string $navigationLabel = 'Matching Suggestions';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('suggestion_ref')->required(), TextInput::make('source_type')->required(), TextInput::make('source_id')->required(), TextInput::make('target_type')->required(), TextInput::make('target_id')->required(), TextInput::make('match_type')->required(), TextInput::make('confidence')->numeric()->required()->rule('between:0,1'), Textarea::make('explanation')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('suggestion_ref')->searchable(), TextColumn::make('match_type')->searchable(), TextColumn::make('confidence')->sortable(), TextColumn::make('status')->badge(), TextColumn::make('source_id'), TextColumn::make('target_id'), TextColumn::make('created_at')->dateTime()->sortable()])->defaultSort('confidence', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListMatchingSuggestions::route('/')];
    }
}
