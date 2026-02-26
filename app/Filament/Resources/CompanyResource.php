<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Filament\Resources\CompanyResource\RelationManagers;
use App\Models\Company;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    
    protected static ?string $navigationGroup = 'Пользователи';
    protected static ?string $navigationLabel = 'Компании';
    protected static ?string $modelLabel = 'Компания';
    protected static ?string $pluralModelLabel = 'Компании';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('inn'),
                Forms\Components\TextInput::make('contact_name')
                    ->required(),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(),
                Forms\Components\Textarea::make('address')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\Select::make('manager_user_id')
                    ->label('Менеджер')
                    ->options(fn () => User::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('Не назначен')
                    ->nullable(),
                Forms\Components\Select::make('subscription_plan_id')
                    ->label('Тариф')
                    ->options(fn () => SubscriptionPlan::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('Без тарифа')
                    ->nullable(),
                Forms\Components\DateTimePicker::make('plan_started_at'),
                Forms\Components\TextInput::make('plan_status')
                    ->required(),
                Forms\Components\TextInput::make('billing_day')
                    ->numeric(),
                Forms\Components\TextInput::make('balance')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('inn')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('manager.name')
                    ->label('Менеджер')
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('subscription_plan_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('plan_started_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('plan_status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('billing_day')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('balance')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'active' => 'Активный',
                        'inactive' => 'Неактивный',
                        'suspended' => 'Приостановлен',
                    ]),
                Tables\Filters\SelectFilter::make('plan_status')
                    ->label('Статус тарифа')
                    ->options([
                        'active' => 'Активный',
                        'trial' => 'Пробный',
                        'expired' => 'Истёк',
                    ]),
                Tables\Filters\SelectFilter::make('manager_user_id')
                    ->label('Менеджер')
                    ->options(fn () => User::orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
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
            RelationManagers\UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
