<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountReconciliationsFilament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\AccountReconciliations\Models\AccountReconciliation;
use Liberu\Accounting\AccountReconciliationsFilament\Resources\AccountReconciliationResource\Pages\ListAccountReconciliations;

final class AccountReconciliationResource extends Resource
{
    protected static ?string $model = AccountReconciliation::class;

    protected static ?string $navigationLabel = 'Account Reconciliations';

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('book_id')->numeric()->required(), TextInput::make('account_id')->numeric()->required(), DatePicker::make('period_start')->required(), DatePicker::make('period_end')->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('account_id')->sortable(), TextColumn::make('period_start')->date(), TextColumn::make('period_end')->date(), TextColumn::make('status')->badge(), TextColumn::make('preparer.user_id'), TextColumn::make('reviewer.user_id')])->defaultSort('period_end', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => ListAccountReconciliations::route('/')];
    }
}
