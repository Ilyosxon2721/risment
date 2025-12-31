<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\ShipmentFbo;
use App\Models\ShipmentItem;
use App\Models\Inventory;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function index(Request $request)
    {
        $company = $request->attributes->get('currentCompany');
        
        $shipments = ShipmentFbo::where('company_id', $company->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('cabinet.shipments.index', compact('shipments'));
    }
    
    public function create(Request $request)
    {
        $company = $request->attributes->get('currentCompany');
        
        // Get available inventory
        $inventory = Inventory::where('company_id', $company->id)
            ->with('sku')
            ->whereRaw('qty_total > qty_reserved')
            ->get();
        
        return view('cabinet.shipments.create', compact('inventory'));
    }
    
    public function store(Request $request)
    {
        $company = $request->attributes->get('currentCompany');
        
        $validated = $request->validate([
            'marketplace' => 'required|in:uzum,wb,ozon,yandex',
            'warehouse_name' => 'required|string|max:255',
            'planned_at' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.sku_id' => 'required|exists:skus,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);
        
        // Create shipment
        $shipment = ShipmentFbo::create([
            'company_id' => $company->id,
            'marketplace' => $validated['marketplace'],
            'warehouse_name' => $validated['warehouse_name'],
            'planned_at' => $validated['planned_at'] ?? null,
            'status' => 'draft',
            'notes' => $validated['notes'] ?? null,
        ]);
        
        // Create items and reserve inventory
        foreach ($validated['items'] as $item) {
            ShipmentItem::create([
                'shipment_id' => $shipment->id,
                'sku_id' => $item['sku_id'],
                'qty' => $item['qty'],
            ]);
            
            // Reserve inventory
            $inventory = Inventory::where('company_id', $company->id)
                ->where('sku_id', $item['sku_id'])
                ->first();
            
            if ($inventory) {
                $inventory->increment('qty_reserved', $item['qty']);
            }
        }
        
        return redirect()->route('cabinet.shipments.show', $shipment)
            ->with('success', __('Shipment created successfully'));
    }
    
    public function show(Request $request, ShipmentFbo $shipment)
    {
        $company = $request->attributes->get('currentCompany');
        
        if ($shipment->company_id !== $company->id) {
            abort(403);
        }
        
        $shipment->load('items.sku');
        
        return view('cabinet.shipments.show', compact('shipment'));
    }
}
