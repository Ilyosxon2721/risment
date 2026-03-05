<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Sku;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $company = $request->attributes->get('managerCompany');

        $query = Inventory::where('company_id', $company->id)
            ->with('sku');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('sku', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        // Filter by stock status
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $query->whereRaw('qty_total > 0');
                    break;
                case 'low_stock':
                    $query->whereRaw('qty_total > 0 AND qty_total <= 10');
                    break;
                case 'out_of_stock':
                    $query->where('qty_total', 0);
                    break;
            }
        }

        $inventory = $query->orderBy('updated_at', 'desc')->paginate(20)->withQueryString();

        // Summary stats
        $stats = [
            'total_skus' => Inventory::where('company_id', $company->id)->count(),
            'total_units' => Inventory::where('company_id', $company->id)->sum('qty_total'),
            'reserved_units' => Inventory::where('company_id', $company->id)->sum('qty_reserved'),
            'available_units' => Inventory::where('company_id', $company->id)->selectRaw('SUM(qty_total - qty_reserved) as available')->value('available') ?? 0,
        ];

        return view('manager.inventory.index', compact('inventory', 'stats'));
    }

    public function show(Request $request, Inventory $inventory)
    {
        $company = $request->attributes->get('managerCompany');
        abort_unless($inventory->company_id === $company->id, 403);

        $inventory->load('sku');

        return view('manager.inventory.show', compact('inventory'));
    }

    public function adjust(Request $request, Inventory $inventory)
    {
        $company = $request->attributes->get('managerCompany');
        abort_unless($inventory->company_id === $company->id, 403);

        $validated = $request->validate([
            'adjustment_type' => 'required|in:add,subtract,set',
            'quantity' => 'required|integer|min:0',
            'reason' => 'required|string|max:255',
        ]);

        $oldQty = $inventory->qty_total;

        switch ($validated['adjustment_type']) {
            case 'add':
                $inventory->increment('qty_total', $validated['quantity']);
                break;
            case 'subtract':
                $newQty = max(0, $inventory->qty_total - $validated['quantity']);
                $inventory->update(['qty_total' => $newQty]);
                break;
            case 'set':
                $inventory->update(['qty_total' => $validated['quantity']]);
                break;
        }

        // TODO: Log adjustment to inventory_movements table

        return redirect()->route('manager.inventory.index')
            ->with('success', "Остаток изменён: {$oldQty} → {$inventory->fresh()->qty_total}");
    }
}
