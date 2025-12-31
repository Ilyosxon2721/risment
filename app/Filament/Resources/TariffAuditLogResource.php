<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TariffAuditLogResource\Pages;
use App\Models\TariffAuditLog;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class TariffAuditLogResource extends Resource
{
    protected static ?string $model = TariffAuditLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    protected static ?string $navigationLabel = 'История изменений';
    
    protected static ?string $modelLabel = 'запись аудита';
    
    protected static ?string $pluralModelLabel = 'История изменений тарифов';
    
    protected static ?string $navigationGroup = 'Ценообразование';
    
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        // Read-only resource, no form needed
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата/время')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->default('Система')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('entity_type')
                    ->label('Тип')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->badge()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('entity_id')
                    ->label('ID')
                    ->numeric(),
                
                Tables\Columns\TextColumn::make('before_json')
                    ->label('Было')
                    ->limit(30)
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('after_json')
                    ->label('Стало')
                    ->limit(30)
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('entity_type')
                    ->label('Тип сущности')
                    ->options([
                        'App\\Models\\PricingRate' => 'Тариф',
                        'App\\Models\\SurchargeTier' => 'Надбавка',
                        'App\\Models\\SubscriptionPlan' => 'План подписки',
                    ]),
                
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('С'),
                        Forms\Components\DatePicker::make('until')->label('По'),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Информация об изменении')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Дата/время')
                            ->dateTime('d.m.Y H:i:s'),
                        
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Пользователь')
                            ->default('Система'),
                        
                        Infolists\Components\TextEntry::make('entity_type')
                            ->label('Тип сущности')
                            ->formatStateUsing(fn ($state) => class_basename($state)),
                        
                        Infolists\Components\TextEntry::make('entity_id')
                            ->label('ID сущности'),
                    ])
                    ->columns(2),
                
                Infolists\Components\Section::make('Значения до изменения')
                    ->schema([
                        Infolists\Components\TextEntry::make('before_json')
                            ->label('')
                            ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
                            ->markdown(),
                    ])
                    ->collapsible(),
                
                Infolists\Components\Section::make('Значения после изменения')
                    ->schema([
                        Infolists\Components\TextEntry::make('after_json')
                            ->label('')
                            ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
                            ->markdown(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTariffAuditLogs::route('/'),
        ];
    }
    
    // Disable create and edit
    public static function canCreate(): bool
    {
        return false;
    }
    
    public static function canEdit($record): bool
    {
        return false;
    }
    
    public static function canDelete($record): bool
    {
        return false;
    }
}
