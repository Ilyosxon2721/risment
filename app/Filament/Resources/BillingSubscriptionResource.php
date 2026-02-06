<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillingSubscriptionResource\Pages;
use App\Models\BillingPlan;
use App\Models\BillingSubscription;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BillingSubscriptionResource extends Resource
{
    protected static ?string $model = BillingSubscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationLabel = 'Подписки';

    protected static ?string $modelLabel = 'подписка';

    protected static ?string $pluralModelLabel = 'Подписки';

    protected static ?string $navigationGroup = 'Финансы';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Подписка')
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->label('Компания')
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn ($record) => $record !== null),

                        Forms\Components\Select::make('billing_plan_id')
                            ->label('Тарифный план')
                            ->options(
                                BillingPlan::active()
                                    ->ordered()
                                    ->get()
                                    ->mapWithKeys(fn ($plan) => [
                                        $plan->id => "{$plan->name_ru} — " . ($plan->monthly_fee > 0
                                            ? number_format($plan->monthly_fee, 0, '', ' ') . ' сум/мес'
                                            : 'бесплатно'),
                                    ])
                            )
                            ->searchable()
                            ->required()
                            ->native(false),

                        Forms\Components\DatePicker::make('started_at')
                            ->label('Дата начала')
                            ->required()
                            ->default(now())
                            ->native(false),

                        Forms\Components\DatePicker::make('expires_at')
                            ->label('Дата окончания')
                            ->nullable()
                            ->native(false)
                            ->helperText('Пусто = бессрочно'),

                        Forms\Components\Select::make('status')
                            ->label('Статус')
                            ->options([
                                'active' => 'Активна',
                                'paused' => 'Приостановлена',
                                'cancelled' => 'Отменена',
                                'expired' => 'Истекла',
                            ])
                            ->required()
                            ->native(false)
                            ->default('active'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Компания')
                    ->searchable()
                    ->sortable()
                    ->url(fn (BillingSubscription $record): string =>
                        CompanyResource::getUrl('edit', ['record' => $record->company_id])),

                Tables\Columns\TextColumn::make('billingPlan.name_ru')
                    ->label('Тариф')
                    ->badge()
                    ->color(fn (BillingSubscription $record): string => match ($record->billingPlan?->code) {
                        'enterprise' => 'danger',
                        'pro' => 'warning',
                        'business' => 'success',
                        'starter' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('billingPlan.monthly_fee')
                    ->label('Абонплата')
                    ->formatStateUsing(fn ($state) => $state > 0
                        ? number_format($state, 0, '', ' ') . ' сум'
                        : 'Бесплатно'),

                Tables\Columns\TextColumn::make('started_at')
                    ->label('Начало')
                    ->date('d.m.Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Окончание')
                    ->date('d.m.Y')
                    ->placeholder('Бессрочно')
                    ->sortable()
                    ->color(fn ($state) => $state && $state->isPast() ? 'danger' : null),

                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Активна',
                        'paused' => 'Приостановлена',
                        'cancelled' => 'Отменена',
                        'expired' => 'Истекла',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'paused' => 'warning',
                        'cancelled' => 'danger',
                        'expired' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создана')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('billing_plan_id')
                    ->label('Тариф')
                    ->relationship('billingPlan', 'name_ru'),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'active' => 'Активна',
                        'paused' => 'Приостановлена',
                        'cancelled' => 'Отменена',
                        'expired' => 'Истекла',
                    ]),

                Tables\Filters\Filter::make('expiring_soon')
                    ->label('Истекает скоро')
                    ->query(fn (Builder $query): Builder => $query
                        ->where('status', 'active')
                        ->whereNotNull('expires_at')
                        ->whereBetween('expires_at', [now(), now()->addDays(30)])),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('activate')
                    ->label('Активировать')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->visible(fn (BillingSubscription $record): bool =>
                        in_array($record->status, ['paused', 'cancelled']))
                    ->requiresConfirmation()
                    ->action(function (BillingSubscription $record): void {
                        $record->update(['status' => 'active']);
                        Notification::make()
                            ->title('Подписка активирована')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('pause')
                    ->label('Приостановить')
                    ->icon('heroicon-o-pause')
                    ->color('warning')
                    ->visible(fn (BillingSubscription $record): bool =>
                        $record->status === 'active')
                    ->requiresConfirmation()
                    ->action(function (BillingSubscription $record): void {
                        $record->update(['status' => 'paused']);
                        Notification::make()
                            ->title('Подписка приостановлена')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('cancel')
                    ->label('Отменить')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (BillingSubscription $record): bool =>
                        in_array($record->status, ['active', 'paused']))
                    ->requiresConfirmation()
                    ->modalHeading('Отменить подписку?')
                    ->modalDescription('Подписка будет отменена. Клиент потеряет доступ к преимуществам тарифа.')
                    ->action(function (BillingSubscription $record): void {
                        $record->update(['status' => 'cancelled']);
                        Notification::make()
                            ->title('Подписка отменена')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListBillingSubscriptions::route('/'),
            'create' => Pages\CreateBillingSubscription::route('/create'),
            'edit' => Pages\EditBillingSubscription::route('/{record}/edit'),
        ];
    }
}
