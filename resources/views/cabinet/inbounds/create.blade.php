@extends('cabinet.layout')

@section('title', __('Create Inbound'))

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-h1 font-heading">{{ __('Create Inbound') }}</h1>
            <p class="text-body-m text-text-muted mt-2">{{ __('Plan new product arrival') }}</p>
        </div>
        <a href="{{ route('cabinet.inbounds.index') }}" class="btn btn-secondary">
            ← {{ __('Back') }}
        </a>
    </div>
</div>

<form action="{{ route('cabinet.inbounds.store') }}" method="POST" x-data="inboundForm()">
    @csrf
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="card mb-6">
                <h2 class="text-h3 font-heading mb-6">{{ __('Inbound Information') }}</h2>
                
                <!-- Reference -->
                <div class="mb-6">
                    <label for="reference" class="label">
                        {{ __('Reference') }} <span class="text-error">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="reference" 
                        name="reference" 
                        value="{{ old('reference', 'ASN-' . now()->format('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT)) }}" 
                        class="input w-full @error('reference') border-error @enderror"
                        placeholder="{{ __('e.g. ASN-001') }}"
                        required
                    >
                    @error('reference')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Planned Date -->
                <div class="mb-6">
                    <label for="planned_at" class="label">
                        {{ __('Planned Date') }}
                    </label>
                    <input 
                        type="date" 
                        id="planned_at" 
                        name="planned_at" 
                        value="{{ old('planned_at') }}" 
                        class="input w-full @error('planned_at') border-error @enderror"
                    >
                    @error('planned_at')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Shipping Address -->
                <div class="mb-6">
                    <label for="shipping_address" class="label">
                        Адрес склада отправки <span class="text-error">*</span>
                    </label>
                    <textarea 
                        id="shipping_address" 
                        name="shipping_address" 
                        rows="2" 
                        class="input w-full @error('shipping_address') border-error @enderror"
                        placeholder="г. Ташкент, ул. Примерная, д. 1"
                        required
                    >{{ old('shipping_address') }}</textarea>
                    @error('shipping_address')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Executor Name -->
                <div class="mb-6">
                    <label for="executor_name" class="label">
                        Исполнитель отгрузки <span class="text-error">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="executor_name" 
                        name="executor_name" 
                        value="{{ old('executor_name') }}" 
                        class="input w-full @error('executor_name') border-error @enderror"
                        placeholder="ФИО ответственного"
                        required
                    >
                    @error('executor_name')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Executor Phone -->
                <div class="mb-6">
                    <label for="executor_phone" class="label">
                        Телефон исполнителя
                    </label>
                    <input 
                        type="tel" 
                        id="executor_phone" 
                        name="executor_phone" 
                        value="{{ old('executor_phone') }}" 
                        class="input w-full @error('executor_phone') border-error @enderror"
                        placeholder="+998 90 123 45 67"
                    >
                    @error('executor_phone')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Notes -->
                <div class="mb-6">
                    <label for="notes" class="label">
                        {{ __('Notes') }}
                    </label>
                    <textarea 
                        id="notes" 
                        name="notes" 
                        rows="3" 
                        class="input w-full @error('notes') border-error @enderror"
                        placeholder="{{ __('Additional information...') }}"
                    >{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Items -->
            <div class="card">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-h3 font-heading">{{ __('Items') }}</h2>
                    <button type="button" @click="addItem()" class="btn btn-secondary btn-sm">
                        + {{ __('Add Item') }}
                    </button>
                </div>
                
                <div class="space-y-4">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="p-4 bg-bg-soft rounded-btn border border-brand-border">
                            <div class="flex gap-4">
                                <!-- Product Variant Selection -->
                                <div class="flex-1">
                                    <label class="label">
                                        Товар <span class="text-error">*</span>
                                    </label>
                                    <select 
                                        :name="`items[${index}][variant_id]`" 
 x-model="item.variant_id"
                                        class="input w-full"
                                        required
                                    >
                                        <option value="">Выберите товар</option>
                                        @foreach($variants as $variant)
                                            <option value="{{ $variant->id }}">
                                                {{ $variant->product->title }} - {{ $variant->variant_name }} ({{ $variant->sku_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Quantity -->
                                <div class="w-32">
                                    <label class="label">
                                        {{ __('Quantity') }} <span class="text-error">*</span>
                                    </label>
                                    <input 
                                        type="number" 
                                        :name="`items[${index}][qty_planned]`"
                                        x-model="item.qty_planned"
                                        class="input w-full"
                                        min="1"
                                        required
                                    >
                                </div>
                                
                                <!-- Remove Button -->
                                <div class="flex items-end">
                                    <button 
                                        type="button" 
                                        @click="removeItem(index)"
                                        class="btn btn-ghost text-error h-[42px]"
                                        x-show="items.length > 1"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Item Notes -->
                            <div class="mt-3">
                                <input 
                                    type="text" 
                                    :name="`items[${index}][notes]`"
                                    x-model="item.notes"
                                    class="input w-full"
                                    :placeholder="'{{ __('Notes for this item...') }}'"
                                >
                            </div>
                        </div>
                    </template>
                </div>
                
                @error('items')
                    <p class="text-error text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <!-- Summary Sidebar -->
        <div class="lg:col-span-1">
            <div class="card sticky top-4">
                <h3 class="text-h4 font-heading mb-4">{{ __('Summary') }}</h3>
                
                <div class="space-y-3 text-body-s mb-6">
                    <div class="flex justify-between">
                        <span class="text-text-muted">{{ __('Total Items') }}</span>
                        <span class="font-semibold" x-text="items.length"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-text-muted">{{ __('Total Units') }}</span>
                        <span class="font-semibold" x-text="totalQty()"></span>
                    </div>
                </div>
                
                <div class="border-t border-brand-border pt-4">
                    <button type="submit" class="btn btn-primary w-full">
                        {{ __('Create Inbound') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function inboundForm() {
    return {
        items: [
            { variant_id: '', qty_planned: 1, notes: '' }
        ],
        addItem() {
            this.items.push({ variant_id: '', qty_planned: 1, notes: '' });
        },
        removeItem(index) {
            this.items.splice(index, 1);
        },
        totalQty() {
            return this.items.reduce((sum, item) => sum + parseInt(item.qty_planned || 0), 0);
        }
    }
}
</script>
@endsection
