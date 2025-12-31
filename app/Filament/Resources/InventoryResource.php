<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryResource\Pages;
use App\Filament\Resources\InventoryResource\RelationManagers;
use App\Models\Inventory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InventoryResource extends Resource
{
    protected static ?string $model = Inventory::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube-transparent';
    
    protected static ?string $navigationGroup = 'Каталог';
    protected static ?string $navigationLabel = 'Остатки';
    protected static ?string $modelLabel = 'Остаток';
    protected static ?string $pluralModelLabel = 'Остатки';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('company_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('sku_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('qty_total')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('qty_reserved')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('location_code'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sku_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('qty_total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('qty_reserved')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventories::route('/'),
            'create' => Pages\CreateInventory::route('/create'),
            'edit' => Pages\EditInventory::route('/{record}/edit'),
        ];
    }
}
