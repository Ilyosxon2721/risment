<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Sku;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $company = $request->attributes->get('currentCompany');
        
        $query = Inventory::where('company_id', $company->id)->with('sku');
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('sku', function($q) use ($search) {
                $q->where('sku_code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }
        
        $inventory = $query->paginate(20);
        
        // Summary stats
        $stats = [
            'total_skus' => Sku::where('company_id', $company->id)->count(),
            'total_qty' => Inventory::where('company_id', $company->id)->sum('qty_total'),
            'total_reserved' => Inventory::where('company_id', $company->id)->sum('qty_reserved'),
            'available_qty' => Inventory::where('company_id', $company->id)
                ->selectRaw('SUM(qty_total - qty_reserved) as available')
                ->value('available') ?? 0,
        ];
        
        return view('cabinet.inventory.index', compact('inventory', 'stats'));
    }
}
