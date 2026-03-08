@extends('manager.layout')

@section('title', __('Add task'))

@section('content')
<div class="mb-8">
    <h2 class="text-h2 font-heading">{{ __('Add task') }}</h2>
    <p class="text-text-muted mt-1">{{ __('Record a completed warehouse operation') }}</p>
</div>

<div class="bg-white rounded-card border border-brand-border p-6 max-w-2xl">
    <form method="POST" action="{{ route('manager.tasks.store') }}">
        @csrf

        <!-- Task Type -->
        <div class="mb-6">
            <label class="block text-body-s font-semibold mb-2">{{ __('Task type') }} *</label>
            <select name="task_type" id="task_type" class="input w-full" required onchange="toggleFields()">
                <option value="">{{ __('Select type') }}</option>
                @foreach(\App\Models\ManagerTask::getTaskTypes() as $value => $label)
                    <option value="{{ $value }}" {{ old('task_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('task_type') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Task Date -->
        <div class="mb-6">
            <label class="block text-body-s font-semibold mb-2">{{ __('Execution date') }} *</label>
            <input type="date" name="task_date" class="input w-full" value="{{ old('task_date', date('Y-m-d')) }}" required>
            @error('task_date') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Inbound Fields -->
        <div id="fields_inbound" class="hidden space-y-4 mb-6 p-4 bg-bg-soft rounded-card">
            <h4 class="font-semibold">📦 {{ __('Receiving data') }}</h4>
            <div>
                <label class="block text-body-s font-semibold mb-2">{{ __('Number of boxes') }} *</label>
                <input type="number" name="boxes_count" class="input w-full" min="1" value="{{ old('boxes_count') }}" placeholder="10">
                @error('boxes_count') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-body-s font-semibold mb-2">{{ __('Supply number') }}</label>
                <input type="text" name="reference" class="input w-full" value="{{ old('reference') }}" placeholder="INB-001">
            </div>
        </div>

        <!-- PickPack / Delivery Fields -->
        <div id="fields_shipment" class="hidden space-y-4 mb-6 p-4 bg-bg-soft rounded-card">
            <h4 class="font-semibold">📋 {{ __('Order data') }}</h4>

            @if($shipments->count() > 0)
            <div>
                <label class="block text-body-s font-semibold mb-2">{{ __('Select order from system') }}</label>
                <select name="shipment_id" id="shipment_id" class="input w-full" onchange="toggleManualInput()">
                    <option value="">— {{ __('Or enter manually') }} —</option>
                    @foreach($shipments as $shipment)
                        <option value="{{ $shipment->id }}" {{ old('shipment_id') == $shipment->id ? 'selected' : '' }}>
                            #{{ $shipment->id }} — {{ $shipment->marketplace }} — {{ $shipment->status }} ({{ $shipment->created_at->format('d.m.Y') }})
                        </option>
                    @endforeach
                </select>
                @error('shipment_id') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
            </div>
            @endif

            <div id="manual_shipment_fields" class="{{ $shipments->count() > 0 ? 'hidden' : '' }} space-y-4 pt-4 border-t border-brand-border/50">
                <p class="text-body-s text-text-muted">{{ __('Manual input (if order not in system):') }}</p>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-body-s font-semibold mb-2">{{ __('Unit count') }} *</label>
                        <input type="number" name="items_count" class="input w-full" min="1" value="{{ old('items_count') }}" placeholder="5">
                        @error('items_count') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-body-s font-semibold mb-2">{{ __('Rate (UZS)') }} *</label>
                        <input type="number" name="pickpack_rate" class="input w-full" min="0" step="100" value="{{ old('pickpack_rate') }}" placeholder="5000">
                        @error('pickpack_rate') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-body-s font-semibold mb-2">{{ __('Order number') }}</label>
                        <input type="text" name="order_number" class="input w-full" value="{{ old('order_number') }}" placeholder="ORD-12345">
                    </div>
                </div>
            </div>
        </div>

        <!-- Storage Fields -->
        <div id="fields_storage" class="hidden space-y-4 mb-6 p-4 bg-bg-soft rounded-card">
            <h4 class="font-semibold">🏢 {{ __('Storage data') }}</h4>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-body-s font-semibold mb-2">{{ __('Boxes') }}</label>
                    <input type="number" name="storage_boxes" class="input w-full" min="0" value="{{ old('storage_boxes', 0) }}">
                    @error('storage_boxes') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-body-s font-semibold mb-2">{{ __('Bags') }}</label>
                    <input type="number" name="storage_bags" class="input w-full" min="0" value="{{ old('storage_bags', 0) }}">
                    @error('storage_bags') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Return Fields -->
        <div id="fields_return" class="hidden space-y-4 mb-6 p-4 bg-bg-soft rounded-card">
            <h4 class="font-semibold">↩️ {{ __('Return data') }}</h4>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-body-s font-semibold mb-2">{{ __('Unit count') }} *</label>
                    <input type="number" name="return_qty" class="input w-full" min="1" value="{{ old('return_qty') }}" placeholder="1">
                    @error('return_qty') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-body-s font-semibold mb-2">{{ __('Size category') }}</label>
                    <select name="return_category" class="input w-full">
                        <option value="micro" {{ old('return_category') === 'micro' ? 'selected' : '' }}>MICRO (≤30 {{ __('cm') }})</option>
                        <option value="mgt" {{ old('return_category', 'mgt') === 'mgt' ? 'selected' : '' }}>MGT (31-60 {{ __('cm') }})</option>
                        <option value="sgt" {{ old('return_category') === 'sgt' ? 'selected' : '' }}>SGT (61-120 {{ __('cm') }})</option>
                        <option value="kgt" {{ old('return_category') === 'kgt' ? 'selected' : '' }}>KGT (>120 {{ __('cm') }})</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Shipping/Delivery Fields -->
        <div id="fields_shipping" class="hidden space-y-4 mb-6 p-4 bg-bg-soft rounded-card">
            <h4 class="font-semibold">🚚 {{ __('Delivery data') }}</h4>
            <p class="text-body-s text-text-muted mb-3">{{ __('Specify quantity for each size category:') }}</p>
            <div class="grid grid-cols-4 gap-4">
                <div>
                    <label class="block text-body-s font-semibold mb-2">MICRO (≤30 {{ __('cm') }})</label>
                    <input type="number" name="delivery_micro" class="input w-full" min="0" value="{{ old('delivery_micro', 0) }}">
                </div>
                <div>
                    <label class="block text-body-s font-semibold mb-2">MGT (31-60 {{ __('cm') }})</label>
                    <input type="number" name="delivery_mgt" class="input w-full" min="0" value="{{ old('delivery_mgt', 0) }}">
                </div>
                <div>
                    <label class="block text-body-s font-semibold mb-2">SGT (61-120 {{ __('cm') }})</label>
                    <input type="number" name="delivery_sgt" class="input w-full" min="0" value="{{ old('delivery_sgt', 0) }}">
                </div>
                <div>
                    <label class="block text-body-s font-semibold mb-2">KGT (>120 {{ __('cm') }})</label>
                    <input type="number" name="delivery_kgt" class="input w-full" min="0" value="{{ old('delivery_kgt', 0) }}">
                </div>
            </div>
            <div>
                <label class="block text-body-s font-semibold mb-2">{{ __('Delivery address') }}</label>
                <input type="text" name="delivery_address" class="input w-full" value="{{ old('delivery_address') }}" placeholder="{{ __('Tashkent, Navoi st. 1') }}">
                @error('delivery_address') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-body-s font-semibold mb-2">{{ __('Recipient') }}</label>
                    <input type="text" name="recipient_name" class="input w-full" value="{{ old('recipient_name') }}" placeholder="{{ __('John Doe') }}">
                </div>
                <div>
                    <label class="block text-body-s font-semibold mb-2">{{ __('Phone') }}</label>
                    <input type="text" name="recipient_phone" class="input w-full" value="{{ old('recipient_phone') }}" placeholder="+998901234567">
                </div>
            </div>
        </div>

        <!-- Packaging/Labeling/Photo Fields -->
        <div id="fields_units" class="hidden space-y-4 mb-6 p-4 bg-bg-soft rounded-card">
            <h4 class="font-semibold" id="fields_units_title">{{ __('Service data') }}</h4>
            <div>
                <label class="block text-body-s font-semibold mb-2">{{ __('Unit count') }} *</label>
                <input type="number" name="units_count" class="input w-full" min="1" value="{{ old('units_count') }}" placeholder="10">
                @error('units_count') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Other/Custom Fields -->
        <div id="fields_other" class="hidden space-y-4 mb-6 p-4 bg-bg-soft rounded-card">
            <h4 class="font-semibold">✏️ {{ __('Custom service') }}</h4>
            <div>
                <label class="block text-body-s font-semibold mb-2">{{ __('Amount (UZS)') }} *</label>
                <input type="number" name="custom_amount" class="input w-full" min="0" step="1000" value="{{ old('custom_amount') }}" placeholder="50000">
                @error('custom_amount') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
            </div>
            <p class="text-body-s text-text-muted">{{ __('Specify amount and describe service in comment') }}</p>
        </div>

        <!-- Comment -->
        <div class="mb-6">
            <label class="block text-body-s font-semibold mb-2">{{ __('Comment') }}</label>
            <textarea name="comment" class="input w-full" rows="3" placeholder="{{ __('Additional information...') }}">{{ old('comment') }}</textarea>
            @error('comment') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-4">
            <button type="submit" class="btn-brand px-6 py-3 rounded-btn text-white font-semibold">
                {{ __('Save task') }}
            </button>
            <a href="{{ route('manager.tasks.index') }}" class="px-6 py-3 rounded-btn border border-brand-border hover:bg-bg-soft font-semibold">
                {{ __('Cancel') }}
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleFields() {
    const type = document.getElementById('task_type').value;

    // Hide all field groups
    document.getElementById('fields_inbound').classList.add('hidden');
    document.getElementById('fields_shipment').classList.add('hidden');
    document.getElementById('fields_storage').classList.add('hidden');
    document.getElementById('fields_return').classList.add('hidden');
    document.getElementById('fields_shipping').classList.add('hidden');
    document.getElementById('fields_units').classList.add('hidden');
    document.getElementById('fields_other').classList.add('hidden');

    // Show relevant fields
    switch(type) {
        case 'inbound':
            document.getElementById('fields_inbound').classList.remove('hidden');
            break;
        case 'pickpack':
        case 'delivery':
            document.getElementById('fields_shipment').classList.remove('hidden');
            break;
        case 'shipping':
            document.getElementById('fields_shipping').classList.remove('hidden');
            break;
        case 'storage':
            document.getElementById('fields_storage').classList.remove('hidden');
            break;
        case 'return':
            document.getElementById('fields_return').classList.remove('hidden');
            break;
        case 'packaging':
            document.getElementById('fields_units').classList.remove('hidden');
            document.getElementById('fields_units_title').textContent = '📦 {{ __("Product packaging") }}';
            break;
        case 'labeling':
            document.getElementById('fields_units').classList.remove('hidden');
            document.getElementById('fields_units_title').textContent = '🏷️ {{ __("Product labeling") }}';
            break;
        case 'photo':
            document.getElementById('fields_units').classList.remove('hidden');
            document.getElementById('fields_units_title').textContent = '📸 {{ __("Product photography") }}';
            break;
        case 'inventory_check':
            document.getElementById('fields_units').classList.remove('hidden');
            document.getElementById('fields_units_title').textContent = '📋 {{ __("Inventory check") }}';
            break;
        case 'other':
            document.getElementById('fields_other').classList.remove('hidden');
            break;
    }
}

function toggleManualInput() {
    const shipmentId = document.getElementById('shipment_id');
    const manualFields = document.getElementById('manual_shipment_fields');

    if (shipmentId && manualFields) {
        if (shipmentId.value) {
            manualFields.classList.add('hidden');
        } else {
            manualFields.classList.remove('hidden');
        }
    }
}

// Init on page load (for old() values)
document.addEventListener('DOMContentLoaded', function() {
    toggleFields();
    toggleManualInput();
});
</script>
@endpush
@endsection
