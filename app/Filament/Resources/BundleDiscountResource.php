<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BundleDiscountResource\Pages;
use App\Models\BundleDiscount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BundleDiscountResource extends Resource
{
    protected static ?string $model = BundleDiscount::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';
    
    protected static ?string $navigationLabel = 'Скидки за маркетплейсы';
    
    protected static ?string $modelLabel = 'скидка';
    
    protected static ?string $pluralModelLabel = 'Скидки за маркетплейсы';
    
    protected static ?string $navigationGroup = 'Тарифы';
    
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('marketplaces_count')
                    ->label('Количество маркетплейсов')
                    ->required()
                    ->numeric()
                    ->minValue(2)
                    ->maxValue(10)
                    ->integer()
                    ->unique(ignoreRecord: true)
                    ->helperText('Например: 2, 3, 4'),
                
                Forms\Components\TextInput::make('discount_percent')
                    ->label('Процент скидки')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(50)
                    ->suffix('%')
                    ->helperText('Скидка от базового тарифа'),
                
                Forms\Components\Toggle::make('is_active')
                    ->label('Активна')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('marketplaces_count')
                    ->label('Маркетплейсов')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('discount_percent')
                    ->label('Скидка')
                    ->sortable()
                    ->suffix('%')
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активна')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлена')
                    ->dateTime('d.m.Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Активность'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('marketplaces_count');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBundleDiscounts::route('/'),
            'create' => Pages\CreateBundleDiscount::route('/create'),
            'edit' => Pages\EditBundleDiscount::route('/{record}/edit'),
        ];
    }
}
