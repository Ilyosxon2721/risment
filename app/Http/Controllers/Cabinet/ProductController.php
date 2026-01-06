<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantAttribute;
use App\Models\ProductVariantImage;
use App\Models\MarketplaceProductLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of products for current company
     */
    public function index(Request $request)
    {
        $company = auth()->user()->currentCompany;
        
        $query = Product::where('company_id', $company->id)
            ->with(['variants.images', 'variants.attributes'])
            ->withCount('variants');
        
        // Search
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('article', 'like', "%{$search}%");
            });
        }
        
        // Filter by active
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }
        
        $products = $query->latest()->paginate(20);
        
        return view('cabinet.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        $marketplaces = ['uzum', 'wildberries', 'ozon', 'yandex'];
        
        return view('cabinet.products.create', compact('marketplaces'));
    }

    /**
     * Store a newly created product in storage
     */
    public function store(StoreProductRequest $request)
    {
        $company = auth()->user()->currentCompany;
        
        DB::beginTransaction();
        
        try {
            // Create product
            $product = Product::create([
                'company_id' => $company->id,
                'title' => $request->title,
                'description' => $request->description,
                'article' => $request->article,
                'is_active' => $request->boolean('is_active', true),
            ]);
            
            // Create variants
            foreach ($request->variants as $variantData) {
                $variant = $product->variants()->create([
                    'variant_name' => $variantData['variant_name'],
                    'sku_code' => $variantData['sku_code'],
                    'barcode' => $variantData['barcode'] ?? null,
                    'dims_l' => $variantData['dims_l'] ?? null,
                    'dims_w' => $variantData['dims_w'] ?? null,
                    'dims_h' => $variantData['dims_h'] ?? null,
                    'weight' => $variantData['weight'] ?? null,
                    'price' => $variantData['price'] ?? null,
                    'cost_price' => $variantData['cost_price'] ?? null,
                    'expenses' => $variantData['expenses'] ?? null,
                    'is_active' => $variantData['is_active'] ?? true,
                ]);
                
                // Handle images
                if (isset($variantData['images'])) {
                    foreach ($variantData['images'] as $index => $image) {
                        if ($image) {
                            $path = $image->store('product-images', 'public');
                            
                            $variant->images()->create([
                                'image_path' => $path,
                                'sort_order' => $index,
                                'is_primary' => $index === 0,
                            ]);
                        }
                    }
                }
                
                // Handle attributes
                if (isset($variantData['attributes'])) {
                    foreach ($variantData['attributes'] as $attr) {
                        if (!empty($attr['name']) && !empty($attr['value'])) {
                            $variant->attributes()->create([
                                'attribute_name' => $attr['name'],
                                'attribute_value' => $attr['value'],
                            ]);
                        }
                    }
                }
                
                // Handle marketplace links
                if (isset($variantData['marketplace_links'])) {
                    foreach ($variantData['marketplace_links'] as $link) {
                        if (!empty($link['marketplace']) && !empty($link['marketplace_sku'])) {
                            $variant->marketplaceLinks()->create([
                                'marketplace' => $link['marketplace'],
                                'marketplace_sku' => $link['marketplace_sku'],
                                'marketplace_barcode' => $link['marketplace_barcode'] ?? null,
                                'sync_stock' => $link['sync_stock'] ?? false,
                                'is_active' => true,
                            ]);
                        }
                    }
                }
            }
            
            DB::commit();
            
            return redirect()
                ->route('cabinet.products.index')
                ->with('success', __('Product created successfully'));
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', __('Error creating product: ') . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        
        $product->load([
            'variants.images',
            'variants.attributes',
            'variants.marketplaceLinks'
        ]);
        
        $marketplaces = ['uzum', 'wildberries', 'ozon', 'yandex'];
        
        return view('cabinet.products.edit', compact('product', 'marketplaces'));
    }

    /**
     * Update the specified product in storage
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorize('update', $product);
        
        DB::beginTransaction();
        
        try {
            // Update product
            $product->update([
                'title' => $request->title,
                'description' => $request->description,
                'article' => $request->article,
                'is_active' => $request->boolean('is_active', true),
            ]);
            
            // Track existing variant IDs
            $existingVariantIds = $product->variants()->pluck('id')->toArray();
            $updatedVariantIds = [];
            
            // Update or create variants
            foreach ($request->variants as $variantData) {
                $variantId = $variantData['id'] ?? null;
                
                if ($variantId && in_array($variantId, $existingVariantIds)) {
                    // Update existing variant
                    $variant = ProductVariant::find($variantId);
                    $variant->update([
                        'variant_name' => $variantData['variant_name'],
                        'sku_code' => $variantData['sku_code'],
                        'barcode' => $variantData['barcode'] ?? null,
                        'dims_l' => $variantData['dims_l'] ?? null,
                        'dims_w' => $variantData['dims_w'] ?? null,
                        'dims_h' => $variantData['dims_h'] ?? null,
                        'weight' => $variantData['weight'] ?? null,
                        'price' => $variantData['price'] ?? null,
                        'cost_price' => $variantData['cost_price'] ?? null,
                        'expenses' => $variantData['expenses'] ?? null,
                        'is_active' => $variantData['is_active'] ?? true,
                    ]);
                    
                    $updatedVariantIds[] = $variantId;
                } else {
                    // Create new variant
                    $variant = $product->variants()->create([
                        'variant_name' => $variantData['variant_name'],
                        'sku_code' => $variantData['sku_code'],
                        'barcode' => $variantData['barcode'] ?? null,
                        'dims_l' => $variantData['dims_l'] ?? null,
                        'dims_w' => $variantData['dims_w'] ?? null,
                        'dims_h' => $variantData['dims_h'] ?? null,
                        'weight' => $variantData['weight'] ?? null,
                        'price' => $variantData['price'] ?? null,
                        'cost_price' => $variantData['cost_price'] ?? null,
                        'expenses' => $variantData['expenses'] ?? null,
                        'is_active' => $variantData['is_active'] ?? true,
                    ]);
                    
                    $updatedVariantIds[] = $variant->id;
                }
                
                // Handle new images
                if (isset($variantData['new_images'])) {
                    $currentMaxOrder = $variant->images()->max('sort_order') ?? -1;
                    
                    foreach ($variantData['new_images'] as $index => $image) {
                        if ($image) {
                            $path = $image->store('product-images', 'public');
                            
                            $variant->images()->create([
                                'image_path' => $path,
                                'sort_order' => $currentMaxOrder + $index + 1,
                                'is_primary' => false,
                            ]);
                        }
                    }
                }
                
                // Update attributes (delete and recreate for simplicity)
                $variant->attributes()->delete();
                if (isset($variantData['attributes'])) {
                    foreach ($variantData['attributes'] as $attr) {
                        if (!empty($attr['name']) && !empty($attr['value'])) {
                            $variant->attributes()->create([
                                'attribute_name' => $attr['name'],
                                'attribute_value' => $attr['value'],
                            ]);
                        }
                    }
                }
                
                // Update marketplace links (delete and recreate)
                $variant->marketplaceLinks()->delete();
                if (isset($variantData['marketplace_links'])) {
                    foreach ($variantData['marketplace_links'] as $link) {
                        if (!empty($link['marketplace']) && !empty($link['marketplace_sku'])) {
                            $variant->marketplaceLinks()->create([
                                'marketplace' => $link['marketplace'],
                                'marketplace_sku' => $link['marketplace_sku'],
                                'marketplace_barcode' => $link['marketplace_barcode'] ?? null,
                                'sync_stock' => $link['sync_stock'] ?? false,
                                'is_active' => true,
                            ]);
                        }
                    }
                }
            }
            
            // Delete variants that were removed
            $variantsToDelete = array_diff($existingVariantIds, $updatedVariantIds);
            ProductVariant::whereIn('id', $variantsToDelete)->delete();
            
            DB::commit();
            
            return redirect()
                ->route('cabinet.products.index')
                ->with('success', __('Product updated successfully'));
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', __('Error updating product: ') . $e->getMessage());
        }
    }

    /**
     * Remove the specified product from storage
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        
        DB::beginTransaction();
        
        try {
            // Delete all variant images from storage
            foreach ($product->variants as $variant) {
                foreach ($variant->images as $image) {
                    Storage::disk('public')->delete($image->image_path);
                }
            }
            
            $product->delete();
            
            DB::commit();
            
            return redirect()
                ->route('cabinet.products.index')
                ->with('success', __('Product deleted successfully'));
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->with('error', __('Error deleting product: ') . $e->getMessage());
        }
    }
}
