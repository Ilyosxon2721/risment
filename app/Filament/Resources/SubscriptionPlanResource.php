<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionPlanResource\Pages;
use App\Models\SubscriptionPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubscriptionPlanResource extends Resource
{
    protected static ?string $model = SubscriptionPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    
    protected static ?string $navigationGroup = 'Тарифы';
    
    protected static ?string $navigationLabel = 'Пакеты подписки';
    
    protected static ?int $navigationSort = 1;

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
                            ->helperText('Cannot be changed after creation (used in logic)')
                            ->maxLength(50),
                        
                        Forms\Components\TextInput::make('name_ru')
                            ->label('Display Name (Russian)')
                            ->required()
                            ->maxLength(255)
                            ->helperText('This is what users see on the site'),
                        
                        Forms\Components\TextInput::make('name_uz')
                            ->label('Display Name (Uzbek)')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Foydalanuvchilar uchun ko\'rinadigan nom'),
                        
                        Forms\Components\Textarea::make('description_ru')
                            ->label('Description (Russian)')
                            ->rows(3),
                        
                        Forms\Components\Textarea::make('description_uz')
                            ->label('Description (Uzbek)')
                            ->rows(3),
                    ])->columns(2),
                
                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('price_month')
                            ->required()
                            ->numeric()
                            ->label('Monthly Price (UZS)'),
                        
                        Forms\Components\Toggle::make('is_custom')
                            ->label('Custom/Enterprise Plan'),
                        
                        Forms\Components\TextInput::make('min_price_month')
                            ->numeric()
                            ->label('Minimum Price (for custom plans)'),
                        
                        Forms\Components\TextInput::make('sort')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->label('Sort Order'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Included Limits')
                    ->schema([
                        Forms\Components\TextInput::make('fbs_shipments_included')
                            ->numeric()
                            ->label('FBS Shipments/month'),
                        
                        Forms\Components\TextInput::make('storage_included_boxes')
                            ->numeric()
                            ->label('Storage Boxes (60×40×40)'),
                        
                        Forms\Components\TextInput::make('storage_included_bags')
                            ->numeric()
                            ->label('Storage Bags'),
                        
                        Forms\Components\TextInput::make('inbound_included_boxes')
                            ->numeric()
                            ->label('Inbound Boxes/month'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Features')
                    ->schema([
                        Forms\Components\Toggle::make('shipping_included')
                            ->label('FBS Shipping Included')
                            ->default(true),
                        
                        Forms\Components\Toggle::make('priority_processing')
                            ->label('Priority Processing'),
                        
                        Forms\Components\Toggle::make('sla_high')
                            ->label('High SLA'),
                        
                        Forms\Components\Toggle::make('personal_manager')
                            ->label('Personal Manager'),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])->columns(3),
                
                Forms\Components\Section::make('Overage Fees (UZS)')
                    ->schema([
                        Forms\Components\TextInput::make('over_fbs_mgt_fee')
                            ->numeric()
                            ->default(11000)
                            ->label('Per FBS MGT Shipment')
                            ->helperText('Малогабарит (Pick&Pack + доставка MGT)'),
                        
                        Forms\Components\TextInput::make('over_fbs_sgt_fee')
                            ->numeric()
                            ->default(15000)
                            ->label('Per FBS SGT Shipment')
                            ->helperText('Среднегабарит (Pick&Pack + доставка SGT)'),
                        
                        Forms\Components\TextInput::make('over_fbs_kgt_fee')
                            ->numeric()
                            ->default(27000)
                            ->label('Per FBS KGT Shipment')
                            ->helperText('Крупногабарит (Pick&Pack + доставка KGT)'),
                        
                        Forms\Components\TextInput::make('over_storage_box_fee')
                            ->numeric()
                            ->default(300)
                            ->label('Per Storage Box/month'),
                        
                        Forms\Components\TextInput::make('over_storage_bag_fee')
                            ->numeric()
                            ->default(500)
                            ->label('Per Storage Bag/month'),
                        
                        Forms\Components\TextInput::make('over_inbound_box_fee')
                            ->numeric()
                            ->default(3000)
                            ->label('Per Inbound Box'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->color('gray'),
                
                Tables\Columns\TextColumn::make('name_ru')
                    ->label('Name (RU)')
                    ->searchable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('name_uz')
                    ->label('Name (UZ)')
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('price_month')
                    ->label('Price/month')
                    ->money('UZS', divideBy: 1)
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('fbs_shipments_included')
                    ->label('FBS Shipments')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('shipping_included')
                    ->label('Shipping')
                    ->boolean(),
                
                Tables\Columns\IconColumn::make('priority_processing')
                    ->label('Priority')
                    ->boolean(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('sort')
                    ->sortable(),
            ])
            ->defaultSort('sort')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Only'),
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
            'index' => Pages\ListSubscriptionPlans::route('/'),
            'create' => Pages\CreateSubscriptionPlan::route('/create'),
            'edit' => Pages\EditSubscriptionPlan::route('/{record}/edit'),
        ];
    }
}
