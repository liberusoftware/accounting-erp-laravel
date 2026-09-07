<?php

declare(strict_types=1);

namespace Liberu\Accounting\ThreeWayMatchingFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Liberu\Accounting\ThreeWayMatching\Models\MatchRecord;

final class MatchRecordResource extends Resource
{
    protected static ?string $model = MatchRecord::class;

    protected static ?string $navigationLabel = 'Three-Way Matches';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('purchase_order_id')->required(), TextInput::make('receipt_id')->required(), TextInput::make('bill_id')->required(), TextInput::make('ordered_quantity')->numeric()->required(), TextInput::make('received_quantity')->numeric()->required(), TextInput::make('billed_quantity')->numeric()->required(), TextInput::make('ordered_unit_price')->numeric()->required(), TextInput::make('billed_unit_price')->numeric()->required(), TextInput::make('quantity_tolerance')->numeric()->default(0), TextInput::make('price_tolerance')->numeric()->default(0), TextInput::make('tax_tolerance')->numeric()->default(0)]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('purchase_order_id')->label('PO')->searchable(), TextColumn::make('receipt_id')->label('Receipt')->searchable(), TextColumn::make('bill_id')->label('Bill')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('exceptions_count')->counts('exceptions'), TextColumn::make('created_at')->dateTime()->sortable()])->filters([SelectFilter::make('status')->options(['matched' => 'Matched', 'partial' => 'Partial', 'exception' => 'Exception', 'approved' => 'Approved', 'rejected' => 'Rejected'])])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListMatches::route('/')];
    }
}
