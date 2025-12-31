@extends('cabinet.layout')

@section('title', __('Edit Inbound'))

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-h1 font-heading">{{ __('Edit Inbound') }} #{{ $inbound->reference }}</h1>
            <p class="text-body-m text-text-muted mt-2">{{ __('Modify inbound details and items') }}</p>
        </div>
        <a href="{{ route('cabinet.inbounds.show', ['inbound' => $inbound]) }}" class="btn btn-secondary">
            ← {{ __('Cancel') }}
        </a>
    </div>
</div>

<form action="{{ route('cabinet.inbounds.update', ['inbound' => $inbound]) }}" method="POST" x-data="inboundForm()">
    @csrf
    @method('PUT')
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="card mb-6">
                <h2 class="text-h3 font-heading mb-6">{{ __('Inbound Information') }}</h2>
                
                <!-- Reference -->
                <div class="mb-6">
                    <label for="reference" class="block text-body-m font-semibold text-brand-dark mb-2">
                        {{ __('Reference') }} *
                    </label>
                    <input 
                        type="text" 
                        id="reference" 
                        name="reference" 
                        value="{{ old('reference', $inbound->reference) }}" 
                        class="input w-full @error('reference') border-error @enderror"
                        required
                    >
                    @error('reference')
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
                        value="{{ old('planned_at', $inbound->planned_at ? \Carbon\Carbon::parse($inbound->planned_at)->format('Y-m-d') : '') }}" 
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
                    >{{ old('notes', $inbound->notes) }}</textarea>
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
                
                <div class="space-y-4" id="items-container">
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
                                    class="input w-full"
                                    required
                                >
                                    <option value="">{{ __('Select SKU') }}</option>
                                    @foreach($skus as $sku)
                                        <option value="{{ $sku->id }}">
                                            {{ $sku->sku }} - {{ $sku->name }}
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
                                placeholder="{{ __('Notes for this item...') }}"
                            >
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
                    <button type="submit" class="btn btn-primary w-full mb-3">
                        {{ __('Save Changes') }}
                    </button>
                    <a href="{{ route('cabinet.inbounds.show', ['inbound' => $inbound]) }}" class="btn btn-ghost w-full">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function inboundForm() {
    return {
        items: @json($inbound->items->map(function($item) {
            return [
                'sku_id' => $item->sku_id,
                'qty_planned' => $item->qty_planned,
                'notes' => $item->notes ?? ''
            ];
        })),
        addItem() {
            this.items.push({ sku_id: '', qty_planned: 1, notes: '' });
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
