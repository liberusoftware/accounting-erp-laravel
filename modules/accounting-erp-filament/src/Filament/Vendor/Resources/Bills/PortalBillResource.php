<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\Vendor\Resources\Bills;

use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Liberu\Accounting\Erp\Models\Bill;
use Liberu\Accounting\ErpFilament\Filament\Vendor\Resources\Bills\Pages\ListPortalBills;

/**
 * The vendor's own bills — read-only, scoped to the logged-in vendor.
 */
class PortalBillResource extends Resource
{
    #[\Override]
    protected static ?string $model = Bill::class;

    protected static bool $isScopedToTenant = false;

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    #[\Override]
    protected static ?string $navigationLabel = 'My Bills';

    #[\Override]
    protected static ?string $modelLabel = 'bill';

    #[\Override]
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('vendor_id', Auth::guard('vendor')->id());
    }

    #[\Override]
    public static function canCreate(): bool
    {
        return false;
    }

    #[\Override]
    public static function canEdit(Model $record): bool
    {
        return false;
    }

    #[\Override]
    public static function canDelete(Model $record): bool
    {
        return false;
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bill_number')->searchable()->weight(FontWeight::Bold),
                TextColumn::make('bill_date')->date()->sortable(),
                TextColumn::make('due_date')->date()->sortable(),
                TextColumn::make('total_amount')->money()->sortable(),
                TextColumn::make('amount_paid')->money()->sortable(),
                TextColumn::make('payment_status')->badge(),
            ])
            ->defaultSort('bill_date', 'desc')
            ->toolbarActions([]);
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListPortalBills::route('/'),
        ];
    }
}
