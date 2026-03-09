<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Inbound;
use App\Models\PricingRate;
use App\Models\ShipmentFbo;
use App\Models\ShipmentItem;
use App\Models\Ticket;
use App\Models\MonthlyUsage;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $company = $request->attributes->get('currentCompany');
        $company->load(['subscriptionPlan', 'billingSubscription']);

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
                $usage->fbs_shipments_count ?? 0, // mgtCount (all shipments as MGT estimate)
                0, // sgtCount
                0, // kgtCount
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
        
        // Chart data: Items shipped per month (last 6 months)
        $chartItemsShipped = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $chartItemsShipped[] = ShipmentItem::whereHas('shipment', function ($q) use ($company, $date) {
                $q->where('company_id', $company->id)
                  ->whereYear('created_at', $date->year)
                  ->whereMonth('created_at', $date->month);
            })->sum('qty');
        }

        $chartData = [
            'labels' => $chartLabels,
            'shipments' => $chartShipments,
            'inbounds' => $chartInbounds,
            'items_shipped' => $chartItemsShipped,
        ];

        // Top-10 products by shipment volume (all time)
        $topProducts = ShipmentItem::select('shipment_items.sku_id', DB::raw('SUM(shipment_items.qty) as total_qty'), DB::raw('COUNT(DISTINCT shipment_items.shipment_id) as shipment_count'))
            ->join('shipments_fbo', 'shipments_fbo.id', '=', 'shipment_items.shipment_id')
            ->where('shipments_fbo.company_id', $company->id)
            ->whereNotNull('shipment_items.sku_id')
            ->groupBy('shipment_items.sku_id')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->with('sku')
            ->get();

        // Total items shipped (all time)
        $stats['total_items_shipped'] = ShipmentItem::whereHas('shipment', function ($q) use ($company) {
            $q->where('company_id', $company->id);
        })->sum('qty');

        // Total shipments completed
        $stats['total_shipments'] = ShipmentFbo::where('company_id', $company->id)->count();

        $currentCompany = $company;
        return view('cabinet.dashboard', compact('stats', 'recentInbounds', 'recentShipments', 'plan', 'usage', 'overageEstimate', 'chartData', 'topProducts', 'currentCompany'));
    }

    public function estimate(Request $request, PricingService $pricingService)
    {
        $validated = $request->validate([
            'shipments_count' => 'required|integer|min:0',
            'inbound_boxes' => 'required|integer|min:0',
            'storage_boxes' => 'required|integer|min:0',
        ]);

        $company = $request->attributes->get('currentCompany');
        $plan = $company->subscriptionPlan;

        $shipmentsCount = $validated['shipments_count'];
        $inboundBoxes = $validated['inbound_boxes'];
        $storageBoxDays = $validated['storage_boxes'] * 30;

        if ($plan) {
            $result = $pricingService->calculatePlanCost(
                $plan,
                $shipmentsCount, 0, 0,
                $storageBoxDays, 0,
                $inboundBoxes
            );
        } else {
            $result = $pricingService->calculatePerUnit(
                $shipmentsCount, 0, 0,
                $storageBoxDays, 0,
                $inboundBoxes
            );
        }

        return response()->json($result);
    }
}
