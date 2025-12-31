<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Inbound;
use App\Models\InboundItem;
use App\Models\Sku;
use Illuminate\Http\Request;

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
        $company = $request->attributes->get('currentCompany');
        $skus = Sku::where('company_id', $company->id)->where('is_active', true)->get();
        
        return view('cabinet.inbounds.create', compact('skus'));
    }
    
    public function store(Request $request)
    {
        $company = $request->attributes->get('currentCompany');
        
        $validated = $request->validate([
            'reference' => 'required|string|max:255',
            'planned_at' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.sku_id' => 'required|exists:skus,id',
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
        ]);
        
        // Create items
        foreach ($validated['items'] as $item) {
            InboundItem::create([
                'inbound_id' => $inbound->id,
                'sku_id' => $item['sku_id'],
                'qty_planned' => $item['qty_planned'],
                'notes' => $item['notes'] ?? null,
            ]);
        }
        
        return redirect()->route('cabinet.inbounds.show', $inbound)
            ->with('success', __('Inbound created successfully'));
    }
    
    public function show(Request $request, Inbound $inbound)
    {
        $company = $request->attributes->get('currentCompany');
        
        // Ensure inbound belongs to current company
        if ($inbound->company_id !== $company->id) {
            abort(403);
        }
        
        $inbound->load('items.sku');
        
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
        
        $inbound->load('items.sku');
        $skus = Sku::where('company_id', $company->id)->where('is_active', true)->get();
        
        return view('cabinet.inbounds.edit', compact('inbound', 'skus'));
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
            'items.*.sku_id' => 'required|exists:skus,id',
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
                'sku_id' => $item['sku_id'],
                'qty_planned' => $item['qty_planned'],
                'notes' => $item['notes'] ?? null,
            ]);
        }
        
        return redirect()->route('cabinet.inbounds.show', $inbound)
            ->with('success', __('Inbound updated successfully'));
    }
}
