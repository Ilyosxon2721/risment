<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Inbound;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InboundController extends Controller
{
    public function index(Request $request)
    {
        $company = $request->attributes->get('managerCompany');

        $query = Inbound::where('company_id', $company->id)
            ->with('items.sku');

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $inbounds = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        // Stats
        $stats = [
            'total' => Inbound::where('company_id', $company->id)->count(),
            'pending' => Inbound::where('company_id', $company->id)->whereIn('status', ['draft', 'submitted'])->count(),
            'received' => Inbound::where('company_id', $company->id)->where('status', 'received')->count(),
        ];

        return view('manager.inbounds.index', compact('inbounds', 'stats'));
    }

    public function show(Request $request, Inbound $inbound)
    {
        $company = $request->attributes->get('managerCompany');
        abort_unless($inbound->company_id === $company->id, 403);

        $inbound->load('items.sku');

        return view('manager.inbounds.show', compact('inbound'));
    }

    public function receive(Request $request, Inbound $inbound)
    {
        $company = $request->attributes->get('managerCompany');
        abort_unless($inbound->company_id === $company->id, 403);
        abort_unless(in_array($inbound->status, ['draft', 'submitted']), 422, 'Приёмка уже обработана');

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:inbound_items,id',
            'items.*.received_qty' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($inbound, $validated, $company) {
            foreach ($validated['items'] as $itemData) {
                $item = $inbound->items()->find($itemData['id']);
                if ($item) {
                    $item->update(['received_qty' => $itemData['received_qty']]);

                    // Update inventory
                    $inventory = Inventory::firstOrCreate(
                        ['company_id' => $company->id, 'sku_id' => $item->sku_id],
                        ['qty_total' => 0, 'qty_reserved' => 0]
                    );
                    $inventory->increment('qty_total', $itemData['received_qty']);
                }
            }

            $inbound->update([
                'status' => 'received',
                'received_at' => now(),
            ]);
        });

        return redirect()->route('manager.inbounds.index')
            ->with('success', 'Приёмка завершена, остатки обновлены');
    }
}
