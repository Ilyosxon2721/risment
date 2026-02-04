<?php

namespace App\Http\Requests;

use App\Models\ProductVariant;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
        $product = $this->route('product');
        $company = auth()->user()->companies()->first();
        $companyId = $company ? $company->id : null;
        
        return [
            // Product fields
            'title' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:5000',
            'article' => $companyId
                ? "required|string|max:100|unique:products,article,{$product->id},id,company_id,{$companyId}"
                : "required|string|max:100",
            'is_active' => 'sometimes|boolean',
            
            // Variants array
            'variants' => 'required|array|min:1',
            'variants.*.id' => 'nullable|exists:product_variants,id',
            'variants.*.variant_name' => 'required|string|max:255',
            'variants.*.sku_code' => 'required|string|max:100',
            'variants.*.barcode' => 'nullable|string|max:100|distinct',
            
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
            
            // New images (for update)
            'variants.*.new_images' => 'nullable|array|max:10',
            'variants.*.new_images.*' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            
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
            
            'variants.*.new_images.*.image' => __('File must be an image'),
            'variants.*.new_images.*.max' => __('Image must not be larger than 5MB'),
            
            'variants.*.marketplace_links.*.marketplace.in' => __('Invalid marketplace'),
            'variants.*.barcode.distinct' => __('Barcode must be unique across variants'),
            'variants.*.barcode.unique_barcode' => __('This barcode is already in use'),
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            foreach ($this->input('variants', []) as $index => $variant) {
                if (!empty($variant['barcode'])) {
                    $query = ProductVariant::where('barcode', $variant['barcode']);
                    if (!empty($variant['id'])) {
                        $query->where('id', '!=', $variant['id']);
                    }
                    if ($query->exists()) {
                        $validator->errors()->add("variants.{$index}.barcode", __('This barcode is already in use'));
                    }
                }
            }
        });
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
