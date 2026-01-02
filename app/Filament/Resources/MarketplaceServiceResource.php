<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MarketplaceServiceResource\Pages;
use App\Models\MarketplaceService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MarketplaceServiceResource extends Resource
{
    protected static ?string $model = MarketplaceService::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    
    protected static ?string $navigationGroup = 'Тарифы';
    
    protected static ?string $navigationLabel = 'Маркетплейс услуги';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabled(fn ($record) => $record !== null)
                            ->helperText('Cannot be changed after creation')
                            ->maxLength(50),
                        
                        Forms\Components\Select::make('service_group')
                            ->required()
                            ->options([
                                'launch' => 'Launch',
                                'management' => 'Management',
                                'ads_addon' => 'Ads Add-on',
                                'infographics' => 'Infographics',
                            ]),
                        
                        Forms\Components\Select::make('marketplace')
                            ->options([
                                'uzum' => 'Uzum',
                                'wildberries' => 'Wildberries',
                                'ozon' => 'Ozon',
                                'yandex' => 'Yandex',
                                'all' => 'All Marketplaces',
                            ])
                            ->nullable(),
                    ])->columns(3),

                Forms\Components\Section::make('Localized Content')
                    ->schema([
                        Forms\Components\TextInput::make('name_ru')
                            ->required()
                            ->label('Name (Russian)')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('name_uz')
                            ->required()
                            ->label('Name (Uzbek)')
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('description_ru')
                            ->label('Description (Russian)')
                            ->rows(2),
                        
                        Forms\Components\Textarea::make('description_uz')
                            ->label('Description (Uzbek)')
                            ->rows(2),
                        
                        Forms\Components\TextInput::make('unit_ru')
                            ->required()
                            ->label('Unit (Russian)')
                            ->placeholder('разово, в месяц, за товар')
                            ->maxLength(50),
                        
                        Forms\Components\TextInput::make('unit_uz')
                            ->required()
                            ->label('Unit (Uzbek)')
                            ->placeholder('bir marta, oyiga, mahsulot uchun')
                            ->maxLength(50),
                    ])->columns(2),

                Forms\Components\Section::make('Pricing & Limits')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('UZS')
                            ->step(1000),
                        
                        Forms\Components\TextInput::make('sku_limit')
                            ->numeric()
                            ->nullable()
                            ->helperText('SKU limit for packages (optional)'),
                        
                        Forms\Components\TextInput::make('sort')
                            ->required()
                            ->numeric()
                            ->default(0),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ])->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('service_group')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'launch' => 'success',
                        'management' => 'info',
                        'ads_addon' => 'warning',
                        'infographics' => 'gray',
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('marketplace')
                    ->badge()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('name_ru')
                    ->label('Name')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('price')
                    ->money('UZS', divideBy: 1)
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('sku_limit')
                    ->label('SKU Limit')
                    ->default('—'),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('service_group')
                    ->options([
                        'launch' => 'Launch',
                        'management' => 'Management',
                        'ads_addon' => 'Ads Add-on',
                        'infographics' => 'Infographics',
                    ]),
                
                Tables\Filters\SelectFilter::make('marketplace'),
                
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMarketplaceServices::route('/'),
            'create' => Pages\CreateMarketplaceService::route('/create'),
            'edit' => Pages\EditMarketplaceService::route('/{record}/edit'),
        ];
    }
}
