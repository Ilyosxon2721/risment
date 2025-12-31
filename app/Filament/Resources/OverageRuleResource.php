<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OverageRuleResource\Pages;
use App\Models\OverageRule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OverageRuleResource extends Resource
{
    protected static ?string $model = OverageRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    
    protected static ?string $navigationLabel = 'Правила перерасхода';
    
    protected static ?string $modelLabel = 'правило';
    
    protected static ?string $pluralModelLabel = 'Правила перерасхода';
    
    protected static ?string $navigationGroup = 'Ценообразование';
    
    protected static ?int $navigationSort = 3;

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
                            ->disabled(fn ($record) => $record !== null)
                            ->helperText('Код нельзя изменить после создания'),
                        
                        Forms\Components\Select::make('type')
                            ->label('Тип')
                            ->required()
                            ->options([
                                'shipments' => 'Отправки',
                                'storage_boxes' => 'Коробы на хранении',
                                'storage_bags' => 'Мешки на хранении',
                                'inbound_boxes' => 'Приёмка коробов',
                            ]),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Режим расчёта')
                    ->schema([
                        Forms\Components\Select::make('pricing_mode')
                            ->label('Режим ценообразования')
                            ->required()
                            ->options([
                                'per_unit_base' => 'По базовым тарифам (без надбавки)',
                                'fixed_by_category' => 'Фикс по категориям (MGT/SGT/KGT)',
                                'fixed' => 'Фиксированная плата',
                            ])
                            ->live()
                            ->helperText('per_unit_base - для отправок, fixed - для хранения/приёмки'),
                        
                        Forms\Components\TextInput::make('fee_mgt')
                            ->label('Плата за MGT')
                            ->numeric()
                            ->suffix('сум')
                            ->visible(fn ($get) => $get('pricing_mode') === 'fixed_by_category'),
                        
                        Forms\Components\TextInput::make('fee_sgt')
                            ->label('Плата за SGT')
                            ->numeric()
                            ->suffix('сум')
                            ->visible(fn ($get) => $get('pricing_mode') === 'fixed_by_category'),
                        
                        Forms\Components\TextInput::make('fee_kgt')
                            ->label('Плата за KGT')
                            ->numeric()
                            ->suffix('сум')
                            ->visible(fn ($get) => $get('pricing_mode') === 'fixed_by_category'),
                        
                        Forms\Components\TextInput::make('fee')
                            ->label('Фиксированная плата')
                            ->numeric()
                            ->suffix('сум')
                            ->visible(fn ($get) => $get('pricing_mode') === 'fixed'),
                    ])
                    ->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Код')
                    ->searchable()
                    ->badge(),
                
                Tables\Columns\TextColumn::make('type')
                    ->label('Тип')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'shipments' => 'Отправки',
                        'storage_boxes' => 'Коробы',
                        'storage_bags' => 'Мешки',
                        'inbound_boxes' => 'Приёмка',
                        default => $state,
                    })
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('pricing_mode')
                    ->label('Режим')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'per_unit_base' => 'Базовые тарифы',
                        'fixed_by_category' => 'По категориям',
                        'fixed' => 'Фикс',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('fee')
                    ->label('Плата')
                    ->money('UZS', locale: 'ru')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Тип')
                    ->options([
                        'shipments' => 'Отправки',
                        'storage_boxes' => 'Коробы',
                        'storage_bags' => 'Мешки',
                        'inbound_boxes' => 'Приёмка',
                    ]),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOverageRules::route('/'),
            'create' => Pages\CreateOverageRule::route('/create'),
            'edit' => Pages\EditOverageRule::route('/{record}/edit'),
        ];
    }
}
