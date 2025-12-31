<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanySettingsResource\Pages;
use App\Filament\Resources\CompanySettingsResource\RelationManagers;
use App\Models\CompanySettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompanySettingsResource extends Resource
{
    protected static ?string $model = CompanySettings::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    
    protected static ?string $navigationGroup = 'Настройки';
    protected static ?string $navigationLabel = 'Настройки сайта';
    protected static ?string $modelLabel = 'Настройка';
    protected static ?string $pluralModelLabel = 'Настройки сайта';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Общая информация')
                            ->schema([
                                Forms\Components\FileUpload::make('company_logo')
                                    ->label('Логотип компании')
                                    ->image()
                                    ->directory('company')
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        null,
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->maxSize(2048)
                                    ->helperText('Загрузите логотип компании (макс. 2MB). Если не загружен, будет показан текст "RISMENT".')
                                    ->columnSpanFull(),
                                    
                                Forms\Components\TextInput::make('company_name')
                                    ->label('Название компании')
                                    ->required()
                                    ->default('RISMENT')
                                    ->columnSpanFull(),
                                    
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('phone')
                                            ->label('Телефон')
                                            ->tel()
                                            ->placeholder('+998 XX XXX-XX-XX'),
                                            
                                        Forms\Components\TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->placeholder('info@risment.uz'),
                                    ]),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('Адреса')
                            ->schema([
                                Forms\Components\Textarea::make('address_ru')
                                    ->label('Адрес (Русский)')
                                    ->rows(3)
                                    ->columnSpanFull(),
                                    
                                Forms\Components\Textarea::make('address_uz')
                                    ->label('Адрес (Узбекский)')
                                    ->rows(3)
                                    ->columnSpanFull(),
                                    
                                Forms\Components\Textarea::make('warehouse_address_ru')
                                    ->label('Адрес склада (Русский)')
                                    ->rows(3)
                                    ->columnSpanFull(),
                                    
                                Forms\Components\Textarea::make('warehouse_address_uz')
                                    ->label('Адрес склада (Узбекский)')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('Соцсети')
                            ->schema([
                                Forms\Components\TextInput::make('social_facebook')
                                    ->label('Facebook')
                                    ->url()
                                    ->placeholder('https://facebook.com/risment'),
                                    
                                Forms\Components\TextInput::make('social_instagram')
                                    ->label('Instagram')
                                    ->url()
                                    ->placeholder('https://instagram.com/risment'),
                                    
                                Forms\Components\TextInput::make('social_telegram')
                                    ->label('Telegram')
                                    ->placeholder('@risment'),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('Статистика')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('stat_orders')
                                            ->label('Количество заказов')
                                            ->numeric()
                                            ->required()
                                            ->default(0),
                                            
                                        Forms\Components\TextInput::make('stat_sla')
                                            ->label('SLA (%)')
                                            ->numeric()
                                            ->suffix('%')
                                            ->required()
                                            ->default(99),
                                            
                                        Forms\Components\TextInput::make('stat_support')
                                            ->label('Время поддержки')
                                            ->required()
                                            ->default('24/7')
                                            ->placeholder('24/7'),
                                            
                                        Forms\Components\TextInput::make('stat_warehouse_size')
                                            ->label('Размер склада (м²)')
                                            ->numeric()
                                            ->suffix('м²')
                                            ->required()
                                            ->default(1000),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('company_logo')
                    ->label('Логотип')
                    ->circular()
                    ->defaultImageUrl(asset('images/logo-risment.png')),
                    
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Название')
                    ->searchable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable()
                    ->icon('heroicon-o-phone'),
                    
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->icon('heroicon-o-envelope')
                    ->copyable(),
                    
                Tables\Columns\TextColumn::make('stat_orders')
                    ->label('Заказов')
                    ->numeric()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('stat_sla')
                    ->label('SLA')
                    ->suffix('%')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
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
            'index' => Pages\ListCompanySettings::route('/'),
            'create' => Pages\CreateCompanySettings::route('/create'),
            'edit' => Pages\EditCompanySettings::route('/{record}/edit'),
        ];
    }
}
