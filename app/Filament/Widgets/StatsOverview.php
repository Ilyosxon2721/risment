<?php

namespace App\Filament\Widgets;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Ticket;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected function getStats(): array
    {
        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();
        
        // Users stats
        $usersCount = User::count();
        $usersThisMonth = User::where('created_at', '>=', $thisMonth)->count();
        
        // Companies stats
        $companiesCount = Company::count();
        $activeCompanies = Company::where('status', 'active')->count();
        
        // Revenue this month
        $revenueThisMonth = Invoice::where('status', 'paid')
            ->where('updated_at', '>=', $thisMonth)
            ->sum('total');
        
        // Open tickets
        $openTickets = Ticket::whereIn('status', ['open', 'pending'])->count();
        
        // New leads this month
        $newLeads = Lead::where('created_at', '>=', $thisMonth)->count();
        
        return [
            Stat::make('Пользователи', $usersCount)
                ->description("+ {$usersThisMonth} за месяц")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, $usersThisMonth]),
            
            Stat::make('Компании', $companiesCount)
                ->description("{$activeCompanies} активных")
                ->descriptionIcon('heroicon-m-building-office')
                ->color('success'),
            
            Stat::make('Выручка за месяц', number_format($revenueThisMonth, 0, '', ' ') . ' UZS')
                ->description('Оплаченные счета')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            
            Stat::make('Открытые тикеты', $openTickets)
                ->description('Требуют внимания')
                ->descriptionIcon('heroicon-m-chat-bubble-left-ellipsis')
                ->color($openTickets > 5 ? 'danger' : 'warning'),
            
            Stat::make('Новые лиды', $newLeads)
                ->description('За этот месяц')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('info'),
        ];
    }
}
