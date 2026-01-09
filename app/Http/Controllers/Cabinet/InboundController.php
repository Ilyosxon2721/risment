<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Inbound;
use App\Models\InboundItem;
use App\Models\ProductVariant;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InboundController extends Controller
{
    public function index(Request $request)
    {
        $company = $request->attributes->get('currentCompany');
        
        $inbounds = Inbound::where('company_id', $company->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('cabinet.inbounds.index', compact('inbounds'));
    }
    
    public function create(Request $request)
    {
        $company = auth()->user()->companies()->first();
        if (!$company) {
            abort(403, 'No company assigned');
        }
        
        // Get product variants from this company's products
        $variants = ProductVariant::whereHas('product', function ($q) use ($company) {
            $q->where('company_id', $company->id)->where('is_active', true);
        })->with('product')->get();
        
        return view('cabinet.inbounds.create', compact('variants'));
    }
    
    public function store(Request $request)
    {
        $company = auth()->user()->companies()->first();
        if (!$company) {
            abort(403, 'No company assigned');
        }
        
        $validated = $request->validate([
            'reference' => 'required|string|max:255',
            'planned_at' => 'nullable|date',
            'notes' => 'nullable|string',
            'shipping_address' => 'required|string',
            'executor_name' => 'required|string|max:255',
            'executor_phone' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.qty_planned' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
        ]);
        
        // Create inbound
        $inbound = Inbound::create([
            'company_id' => $company->id,
            'reference' => $validated['reference'],
            'planned_at' => $validated['planned_at'] ?? null,
            'status' => 'draft',
            'notes' => $validated['notes'] ?? null,
            'shipping_address' => $validated['shipping_address'],
            'executor_name' => $validated['executor_name'],
            'executor_phone' => $validated['executor_phone'] ?? null,
        ]);
        
        // Create items
        foreach ($validated['items'] as $item) {
            InboundItem::create([
                'inbound_id' => $inbound->id,
                'variant_id' => $item['variant_id'],
                'qty_planned' => $item['qty_planned'],
                'notes' => $item['notes'] ?? null,
            ]);
        }
        
        return redirect()->route('cabinet.inbounds.show', $inbound)
            ->with('success', __('Inbound created successfully'));
    }
    
    public function submit(Request $request, Inbound $inbound)
    {
        $company = auth()->user()->companies()->first();
        if (!$company || $inbound->company_id !== $company->id) {
            abort(403);
        }
        
        if ($inbound->status !== 'draft') {
            return back()->with('error', 'Можно отправить только черновики');
        }
        
        $inbound->update(['status' => 'submitted']);
        
        return redirect()->route('cabinet.inbounds.show', $inbound)
            ->with('success', 'Поставка отправлена на склад');
    }

    public function confirm(Inbound $inbound, InventoryService $inventoryService)
    {
        if ($inbound->status !== 'completed' || !$inbound->has_discrepancies) {
            return back()->with('error', 'Эта поставка не требует подтверждения');
        }

        DB::transaction(function () use ($inbound, $inventoryService) {
            $inbound->update([
                'status' => 'closed',
                'confirmed_at' => now(),
                'confirmed_by_client' => auth()->id(),
            ]);

            $inventoryService->updateFromInbound($inbound);
        });

        return back()->with('success', 'Расхождения подтверждены, поставка закрыта и товар добавлен на остатки');
    }
    
    public function show(Request $request, Inbound $inbound)
    {
        $company = $request->attributes->get('currentCompany');
        
        // Ensure inbound belongs to current company
        if ($inbound->company_id !== $company->id) {
            abort(403);
        }
        
        $inbound->load('items.variant.product');
        
        return view('cabinet.inbounds.show', compact('inbound'));
    }
    
    public function edit(Request $request, Inbound $inbound)
    {
        $company = $request->attributes->get('currentCompany');
        
        if ($inbound->company_id !== $company->id) {
            abort(403);
        }
        
        // Only allow editing drafts
        if ($inbound->status !== 'draft') {
            return back()->with('error', __('Can only edit draft inbounds'));
        }
        
        $inbound->load('items.variant.product');
        $variants = ProductVariant::whereHas('product', function ($q) use ($company) {
            $q->where('company_id', $company->id)->where('is_active', true);
        })->with('product')->get();
        
        return view('cabinet.inbounds.edit', compact('inbound', 'variants'));
    }
    
    public function update(Request $request, Inbound $inbound)
    {
        $company = $request->attributes->get('currentCompany');
        
        if ($inbound->company_id !== $company->id) {
            abort(403);
        }
        
        if ($inbound->status !== 'draft') {
            return back()->with('error', __('Can only edit draft inbounds'));
        }
        
        $validated = $request->validate([
            'reference' => 'required|string|max:255',
            'planned_at' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.qty_planned' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
        ]);
        
        $inbound->update([
            'reference' => $validated['reference'],
            'planned_at' => $validated['planned_at'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);
        
        // Delete old items and create new ones
        $inbound->items()->delete();
        
        foreach ($validated['items'] as $item) {
            InboundItem::create([
                'inbound_id' => $inbound->id,
                'variant_id' => $item['variant_id'],
                'qty_planned' => $item['qty_planned'],
                'notes' => $item['notes'] ?? null,
            ]);
        }
        
        return redirect()->route('cabinet.inbounds.show', $inbound)
            ->with('success', __('Inbound updated successfully'));
    }
}
