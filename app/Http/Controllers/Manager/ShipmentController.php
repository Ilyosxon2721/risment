<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ShipmentFbo;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function index(Request $request)
    {
        $company = $request->attributes->get('managerCompany');

        $query = ShipmentFbo::where('company_id', $company->id)
            ->with('items');

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Marketplace filter
        if ($request->filled('marketplace')) {
            $query->where('marketplace', $request->marketplace);
        }

        $shipments = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        // Stats
        $stats = [
            'total' => ShipmentFbo::where('company_id', $company->id)->count(),
            'pending' => ShipmentFbo::where('company_id', $company->id)->whereIn('status', ['draft', 'submitted'])->count(),
            'in_progress' => ShipmentFbo::where('company_id', $company->id)->whereIn('status', ['picking', 'packed'])->count(),
            'shipped' => ShipmentFbo::where('company_id', $company->id)->where('status', 'shipped')->count(),
        ];

        return view('manager.shipments.index', compact('shipments', 'stats'));
    }

    public function show(Request $request, ShipmentFbo $shipment)
    {
        $company = $request->attributes->get('managerCompany');
        abort_unless($shipment->company_id === $company->id, 403);

        $shipment->load('items.sku');

        return view('manager.shipments.show', compact('shipment'));
    }

    public function updateStatus(Request $request, ShipmentFbo $shipment)
    {
        $company = $request->attributes->get('managerCompany');
        abort_unless($shipment->company_id === $company->id, 403);

        $validated = $request->validate([
            'status' => 'required|in:draft,submitted,picking,packed,shipped,delivered,cancelled',
        ]);

        $oldStatus = $shipment->status;
        $shipment->update(['status' => $validated['status']]);

        return redirect()->back()
            ->with('success', "Статус изменён: {$oldStatus} → {$validated['status']}");
    }
}
