<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Filament\Resources\CompanyResource\RelationManagers;
use App\Models\BillingBalance;
use App\Models\BillingBalanceTransaction;
use App\Models\BillingItem;
use App\Models\Company;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

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
                    ->placeholder('Не назначен')
                    ->nullable(),
                Forms\Components\Select::make('subscription_plan_id')
                    ->label('Тариф')
                    ->options(fn () => SubscriptionPlan::orderBy('name_ru')->pluck('name_ru', 'id'))
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
                Tables\Columns\TextColumn::make('billing_balance')
                    ->label('Баланс')
                    ->state(fn (Company $record): string => (function () use ($record) {
                        $b = $record->billingBalance?->balance ?? 0;
                        $sign = $b < 0 ? '−' : '+';
                        return $sign . number_format(abs($b), 0, '', ' ') . ' UZS';
                    })())
                    ->color(fn (Company $record): string =>
                        ($record->billingBalance?->balance ?? 0) < 0 ? 'danger' : 'success'
                    ),
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
                    ->options(fn () => User::orderBy('name')->pluck('name', 'id')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('topup')
                    ->label('Пополнить')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('Сумма (UZS)')
                            ->numeric()->minValue(1)->required(),
                        Forms\Components\TextInput::make('description')
                            ->label('Описание')
                            ->default('Пополнение баланса')
                            ->required()->maxLength(255),
                    ])
                    ->action(function (Company $record, array $data): void {
                        $balance = BillingBalance::getOrCreate($record->id);
                        $balance->topup((float) $data['amount'], $data['description']);
                        Notification::make()
                            ->title('Баланс пополнен: ' . number_format($data['amount'], 0, '', ' ') . ' UZS')
                            ->success()->send();
                    }),
                Tables\Actions\Action::make('sync_balance')
                    ->label('Синх. баланс')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Синхронизировать баланс')
                    ->modalDescription('Спишет разницу между начисленными услугами и уже записанными транзакциями за текущий месяц.')
                    ->action(function (Company $record): void {
                        $period = now()->format('Y-m');

                        $itemsTotal = BillingItem::forCompany($record->id)
                            ->forPeriod($period)
                            ->whereIn('status', [BillingItem::STATUS_ACCRUED, BillingItem::STATUS_INVOICED])
                            ->sum('amount');

                        if ($itemsTotal <= 0) {
                            Notification::make()->title('Нет начислений за ' . $period)->warning()->send();
                            return;
                        }

                        $alreadyCharged = abs((float) BillingBalanceTransaction::where('company_id', $record->id)
                            ->where('type', 'charge')
                            ->whereYear('created_at', substr($period, 0, 4))
                            ->whereMonth('created_at', substr($period, 5, 2))
                            ->sum('amount'));

                        $effectiveTotal = $record->applyDiscounts((float) $itemsTotal, 'overage');
                        $gap = $effectiveTotal - $alreadyCharged;

                        if ($gap <= 0) {
                            Notification::make()->title('Баланс уже синхронизирован')->success()->send();
                            return;
                        }

                        $balance = BillingBalance::getOrCreate($record->id);
                        $balance->charge($gap, "Синхронизация начислений за {$period}", BillingItem::class, null);

                        Notification::make()
                            ->title('Списано: ' . number_format($gap, 0, '', ' ') . ' UZS')
                            ->success()->send();
                    }),
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
            RelationManagers\DiscountsRelationManager::class,
            RelationManagers\BalanceTransactionsRelationManager::class,
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
