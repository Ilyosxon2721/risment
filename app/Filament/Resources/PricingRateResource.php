<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PricingRateResource\Pages;
use App\Models\PricingRate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PricingRateResource extends Resource
{
    protected static ?string $model = PricingRate::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    
    protected static ?string $navigationLabel = 'Тарифы услуг';
    
    protected static ?string $modelLabel = 'тариф';
    
    protected static ?string $pluralModelLabel = 'Тарифы';
    
    protected static ?string $navigationGroup = 'Тарифы';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Код')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->disabled(fn ($record) => $record !== null) // Immutable after creation
                            ->helperText('Код нельзя изменить после создания'),
                        
                        Forms\Components\TextInput::make('value')
                            ->label('Значение (UZS)')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->suffix('сум'),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Активен')
                            ->default(true),
                    ])
                    ->columns(3),
                
                Forms\Components\Section::make('Единицы измерения')
                    ->schema([
                        Forms\Components\TextInput::make('unit_ru')
                            ->label('Единица (RU)')
                            ->required()
                            ->maxLength(50)
                            ->placeholder('сум за отправку'),
                        
                        Forms\Components\TextInput::make('unit_uz')
                            ->label('Единица (UZ)')
                            ->required()
                            ->maxLength(50)
                            ->placeholder('so\'m har bir buyurtma uchun'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Описание')
                    ->schema([
                        Forms\Components\Textarea::make('description_ru')
                            ->label('Описание (RU)')
                            ->rows(3),
                        
                        Forms\Components\Textarea::make('description_uz')
                            ->label('Описание (UZ)')
                            ->rows(3),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Код')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                
                Tables\Columns\TextColumn::make('value')
                    ->label('Значение')
                    ->money('UZS', locale: 'ru')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('unit_ru')
                    ->label('Единица')
                    ->searchable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('description_ru')
                    ->label('Описание')
                    ->limit(50)
                    ->toggleable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активен')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлён')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Активность')
                    ->placeholder('Все')
                    ->trueLabel('Только активные')
                    ->falseLabel('Только неактивные'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('code');
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
            'index' => Pages\ListPricingRates::route('/'),
            'create' => Pages\CreatePricingRate::route('/create'),
            'edit' => Pages\EditPricingRate::route('/{record}/edit'),
        ];
    }
}
