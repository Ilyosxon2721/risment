<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShipmentFboResource\Pages;
use App\Filament\Resources\ShipmentFboResource\RelationManagers;
use App\Models\ShipmentFbo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShipmentFboResource extends Resource
{
    protected static ?string $model = ShipmentFbo::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    
    protected static ?string $navigationGroup = 'Операции';
    protected static ?string $navigationLabel = 'Отгрузки';
    protected static ?string $modelLabel = 'Отгрузка';
    protected static ?string $pluralModelLabel = 'Отгрузки';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('company_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('marketplace')
                    ->required(),
                Forms\Components\TextInput::make('warehouse_name')
                    ->required(),
                Forms\Components\DateTimePicker::make('planned_at'),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('marketplace')
                    ->searchable(),
                Tables\Columns\TextColumn::make('warehouse_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('planned_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
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
            'index' => Pages\ListShipmentFbos::route('/'),
            'create' => Pages\CreateShipmentFbo::route('/create'),
            'edit' => Pages\EditShipmentFbo::route('/{record}/edit'),
        ];
    }
}
