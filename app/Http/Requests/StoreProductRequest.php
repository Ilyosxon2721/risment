<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $company = auth()->user()->companies()->first();
        $companyId = $company ? $company->id : null;
        
        return [
            // Product fields
            'title' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:5000',
            'article' => $companyId 
                ? "required|string|max:100|unique:products,article,NULL,id,company_id,{$companyId}"
                : "required|string|max:100",
            'is_active' => 'sometimes|boolean',
            
            // Variants array
            'variants' => 'required|array|min:1',
            'variants.*.variant_name' => 'required|string|max:255',
            'variants.*.sku_code' => 'required|string|max:100',
            'variants.*.barcode' => 'nullable|string|max:100',
            
            // Dimensions
            'variants.*.dims_l' => 'nullable|numeric|min:0|max:999',
            'variants.*.dims_w' => 'nullable|numeric|min:0|max:999',
            'variants.*.dims_h' => 'nullable|numeric|min:0|max:999',
            'variants.*.weight' => 'nullable|numeric|min:0|max:99999',
            
            // Pricing (optional)
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.cost_price' => 'nullable|numeric|min:0',
            'variants.*.expenses' => 'nullable|numeric|min:0',
            'variants.*.is_active' => 'sometimes|boolean',
            
            // Images (multiple per variant)
            'variants.*.images' => 'nullable|array|max:10',
            'variants.*.images.*' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120', // 5MB max
            
            // Attributes (dynamic key-value pairs)
            'variants.*.attributes' => 'nullable|array',
            'variants.*.attributes.*.name' => 'required_with:variants.*.attributes.*.value|string|max:100',
            'variants.*.attributes.*.value' => 'required_with:variants.*.attributes.*.name|string|max:255',
            
            // Marketplace links
            'variants.*.marketplace_links' => 'nullable|array',
            'variants.*.marketplace_links.*.marketplace' => 'required_with:variants.*.marketplace_links.*.marketplace_sku|in:uzum,wildberries,ozon,yandex',
            'variants.*.marketplace_links.*.marketplace_sku' => 'required_with:variants.*.marketplace_links.*.marketplace|string|max:255',
            'variants.*.marketplace_links.*.marketplace_barcode' => 'nullable|string|max:255',
            'variants.*.marketplace_links.*.sync_stock' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => __('Product title is required'),
            'article.required' => __('Article is required'),
            'article.unique' => __('This article already exists for your company'),
            
            'variants.required' => __('At least one variant is required'),
            'variants.min' => __('At least one variant is required'),
            
            'variants.*.variant_name.required' => __('Variant name is required'),
            'variants.*.sku_code.required' => __('SKU code is required'),
            
            'variants.*.images.*.image' => __('File must be an image'),
            'variants.*.images.*.max' => __('Image must not be larger than 5MB'),
            
            'variants.*.marketplace_links.*.marketplace.in' => __('Invalid marketplace'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => __('title'),
            'article' => __('article'),
            'variants.*.variant_name' => __('variant name'),
            'variants.*.sku_code' => __('SKU code'),
            'variants.*.barcode' => __('barcode'),
        ];
    }
}
