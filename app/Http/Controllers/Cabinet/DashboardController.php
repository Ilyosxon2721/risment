<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Inbound;
use App\Models\ShipmentFbo;
use App\Models\Ticket;
use App\Models\MonthlyUsage;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $company = $request->attributes->get('currentCompany');
        
        // Get subscription plan
        $plan = $company->subscriptionPlan;
        
        // Get current month usage
        $currentMonth = Carbon::now()->format('Y-m');
        $usage = MonthlyUsage::where('company_id', $company->id)
            ->where('month', $currentMonth)
            ->first();
        
        // Calculate overages if plan exists
        $overageEstimate = null;
        if ($plan && $usage) {
            $overageEstimate = $plan->calculateOverage(
                $usage->fbs_shipments_count ?? 0,
                $usage->storage_boxes_peak ?? 0,
                $usage->storage_bags_peak ?? 0,
                $usage->inbound_boxes_count ?? 0
            );
        }
        
        // Get stats
        $stats = [
            'total_skus' => $company->skus()->count(),
            'total_inventory' => Inventory::where('company_id', $company->id)->sum('qty_total'),
            'pending_inbounds' => Inbound::where('company_id', $company->id)
                ->whereIn('status', ['draft', 'submitted', 'processing'])
                ->count(),
            'active_shipments' => ShipmentFbo::where('company_id', $company->id)
                ->whereIn('status', ['draft', 'submitted', 'picking', 'packed'])
                ->count(),
            'open_tickets' => Ticket::where('company_id', $company->id)
                ->where('status', '!=', 'closed')
                ->count(),
        ];
        
        // Recent inbounds
        $recentInbounds = Inbound::where('company_id', $company->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Recent shipments
        $recentShipments = ShipmentFbo::where('company_id', $company->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Chart data: Shipments per month (last 6 months)
        $chartLabels = [];
        $chartShipments = [];
        $chartInbounds = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $chartLabels[] = $date->translatedFormat('M Y');
            
            $chartShipments[] = ShipmentFbo::where('company_id', $company->id)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $chartInbounds[] = Inbound::where('company_id', $company->id)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }
        
        $chartData = [
            'labels' => $chartLabels,
            'shipments' => $chartShipments,
            'inbounds' => $chartInbounds,
        ];
        
        return view('cabinet.dashboard', compact('stats', 'recentInbounds', 'recentShipments', 'plan', 'usage', 'overageEstimate', 'chartData'));
    }
}
