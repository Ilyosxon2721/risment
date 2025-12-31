@extends('cabinet.layout')

@section('title', __('Create Shipment'))

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-h1 font-heading">{{ __('Create Shipment') }}</h1>
            <p class="text-body-m text-text-muted mt-2">{{ __('Send products to marketplace') }}</p>
        </div>
        <a href="{{ route('cabinet.shipments.index') }}" class="btn btn-secondary">
            ← {{ __('Back') }}
        </a>
    </div>
</div>

<form action="{{ route('cabinet.shipments.store') }}" method="POST" x-data="shipmentForm()">
    @csrf
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="card mb-6">
                <h2 class="text-h3 font-heading mb-6">{{ __('Shipment Information') }}</h2>
                
                <!-- Marketplace -->
                <div class="mb-6">
                    <label for="marketplace" class="block text-body-m font-semibold text-brand-dark mb-2">
                        {{ __('Marketplace') }} *
                    </label>
                    <select 
                        id="marketplace" 
                        name="marketplace" 
                        class="input w-full @error('marketplace') border-error @enderror"
                        required
                    >
                        <option value="">{{ __('Select marketplace') }}</option>
                        <option value="uzum" {{ old('marketplace') === 'uzum' ? 'selected' : '' }}>Uzum</option>
                        <option value="wb" {{ old('marketplace') === 'wb' ? 'selected' : '' }}>Wildberries</option>
                        <option value="ozon" {{ old('marketplace') === 'ozon' ? 'selected' : '' }}>Ozon</option>
                        <option value="yandex" {{ old('marketplace') === 'yandex' ? 'selected' : '' }}>Yandex Market</option>
                    </select>
                    @error('marketplace')
                        <p class="text-error text-body-s mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Warehouse Name -->
                <div class="mb-6">
                    <label for="warehouse_name" class="block text-body-m font-semibold text-brand-dark mb-2">
                        {{ __('Warehouse') }} *
                    </label>
                    <input 
                        type="text" 
                        id="warehouse_name" 
                        name="warehouse_name" 
                        value="{{ old('warehouse_name') }}" 
                        class="input w-full @error('warehouse_name') border-error @enderror"
                        placeholder="{{ __('e.g. Tashkent Main Warehouse') }}"
                        required
                    >
                    @error('warehouse_name')
                        <p class="text-error text-body-s mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Planned Date -->
                <div class="mb-6">
                    <label for="planned_at" class="block text-body-m font-semibold text-brand-dark mb-2">
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
                        <p class="text-error text-body-s mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Notes -->
                <div class="mb-6">
                    <label for="notes" class="block text-body-m font-semibold text-brand-dark mb-2">
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
                        <p class="text-error text-body-s mt-1">{{ $message }}</p>
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
                    <div class="p-4 bg-bg-soft rounded-btn border border-brand-border" x-for="(item, index) in items" :key="index">
                        <div class="flex gap-4">
                            <!-- SKU Selection -->
                            <div class="flex-1">
                                <label class="block text-body-s font-semibold text-brand-dark mb-2">
                                    {{ __('SKU') }} *
                                </label>
                                <select 
                                    :name="`items[${index}][sku_id]`" 
                                    x-model="item.sku_id"
                                    @change="updateAvailable(index)"
                                    class="input w-full"
                                    required
                                >
                                    <option value="">{{ __('Select SKU') }}</option>
                                    @foreach($inventory as $inv)
                                        <option value="{{ $inv->sku_id }}" data-available="{{ $inv->qty_total - $inv->qty_reserved }}">
                                            {{ $inv->sku->sku }} - {{ $inv->sku->name }} 
                                            ({{ __('Available') }}: {{ $inv->qty_total - $inv->qty_reserved }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Quantity -->
                            <div class="w-32">
                                <label class="block text-body-s font-semibold text-brand-dark mb-2">
                                    {{ __('Quantity') }} *
                                </label>
                                <input 
                                    type="number" 
                                    :name="`items[${index}][qty]`"
                                    x-model="item.qty"
                                    :max="item.available"
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
                        <div x-show="item.available" class="mt-2 text-body-s text-text-muted">
                            {{ __('Available') }}: <span x-text="item.available"></span>
                        </div>
                    </div>
                </div>
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
                        {{ __('Create Shipment') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function shipmentForm() {
    return {
        items: [
            { sku_id: '', qty: 1, available: 0 }
        ],
        addItem() {
            this.items.push({ sku_id: '', qty: 1, available: 0 });
        },
        removeItem(index) {
            this.items.splice(index, 1);
        },
        updateAvailable(index) {
            const select = document.querySelectorAll('select[name^="items"]')[index];
            const option = select.options[select.selectedIndex];
            this.items[index].available = parseInt(option.dataset.available || 0);
        },
        totalQty() {
            return this.items.reduce((sum, item) => sum + parseInt(item.qty || 0), 0);
        }
    }
}
</script>
@endsection
