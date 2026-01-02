<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Sku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SkuController extends Controller
{
    /**
     * Display a listing of SKUs.
     */
    public function index(Request $request)
    {
        $company = $request->attributes->get('currentCompany');
        
        $query = Sku::where('company_id', $company->id);
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sku_code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        
        $skus = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('cabinet.skus.index', compact('skus'));
    }

    /**
     * Show the form for creating a new SKU.
     */
    public function create()
    {
        return view('cabinet.skus.create');
    }

    /**
     * Store a newly created SKU.
     */
    public function store(Request $request)
    {
        $company = $request->attributes->get('currentCompany');
        
        $validated = $request->validate([
            'sku_code' => ['required', 'string', 'max:100', 'unique:skus,sku_code,NULL,id,company_id,' . $company->id],
            'barcode' => ['nullable', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'dims_l' => ['nullable', 'numeric', 'min:0'],
            'dims_w' => ['nullable', 'numeric', 'min:0'],
            'dims_h' => ['nullable', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'is_active' => ['boolean'],
        ]);
        
        $validated['company_id'] = $company->id;
        $validated['is_active'] = $request->boolean('is_active', true);
        
        // Handle photo upload
        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('skus/' . $company->id, 'public');
        }
        
        unset($validated['photo']);
        
        Sku::create($validated);
        
        return redirect()->route('cabinet.skus.index')
            ->with('success', __('Product created successfully!'));
    }

    /**
     * Show the form for editing the specified SKU.
     */
    public function edit(Request $request, Sku $sku)
    {
        $company = $request->attributes->get('currentCompany');
        
        if ($sku->company_id !== $company->id) {
            abort(403);
        }
        
        return view('cabinet.skus.edit', compact('sku'));
    }

    /**
     * Update the specified SKU.
     */
    public function update(Request $request, Sku $sku)
    {
        $company = $request->attributes->get('currentCompany');
        
        if ($sku->company_id !== $company->id) {
            abort(403);
        }
        
        $validated = $request->validate([
            'sku_code' => ['required', 'string', 'max:100', 'unique:skus,sku_code,' . $sku->id . ',id,company_id,' . $company->id],
            'barcode' => ['nullable', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'dims_l' => ['nullable', 'numeric', 'min:0'],
            'dims_w' => ['nullable', 'numeric', 'min:0'],
            'dims_h' => ['nullable', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'is_active' => ['boolean'],
        ]);
        
        $validated['is_active'] = $request->boolean('is_active', true);
        
        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($sku->photo_path) {
                Storage::disk('public')->delete($sku->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('skus/' . $company->id, 'public');
        }
        
        unset($validated['photo']);
        
        $sku->update($validated);
        
        return redirect()->route('cabinet.skus.index')
            ->with('success', __('Product updated successfully!'));
    }

    /**
     * Remove the specified SKU.
     */
    public function destroy(Request $request, Sku $sku)
    {
        $company = $request->attributes->get('currentCompany');
        
        if ($sku->company_id !== $company->id) {
            abort(403);
        }
        
        // Check if SKU has inventory or shipments
        if ($sku->inventory && $sku->inventory->qty_total > 0) {
            return back()->with('error', __('Cannot delete product with inventory. Please remove inventory first.'));
        }
        
        // Delete photo
        if ($sku->photo_path) {
            Storage::disk('public')->delete($sku->photo_path);
        }
        
        $sku->delete();
        
        return redirect()->route('cabinet.skus.index')
            ->with('success', __('Product deleted successfully!'));
    }
}
