@extends('manager.layout')

@section('title', __('Inventory'))

@section('content')
<div class="mb-8">
    <h2 class="text-xl sm:text-h2 font-heading">{{ __('Client inventory') }}</h2>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-card border border-brand-border p-4">
        <div class="text-body-s text-text-muted">{{ __('Total SKUs') }}</div>
        <div class="text-h3 font-heading mt-1">{{ number_format($stats['total_skus']) }}</div>
    </div>
    <div class="bg-white rounded-card border border-brand-border p-4">
        <div class="text-body-s text-text-muted">{{ __('Total units') }}</div>
        <div class="text-h3 font-heading mt-1">{{ number_format($stats['total_units']) }}</div>
    </div>
    <div class="bg-white rounded-card border border-brand-border p-4">
        <div class="text-body-s text-text-muted">{{ __('Reserved') }}</div>
        <div class="text-h3 font-heading mt-1 text-warning">{{ number_format($stats['reserved_units']) }}</div>
    </div>
    <div class="bg-white rounded-card border border-brand-border p-4">
        <div class="text-body-s text-text-muted">{{ __('Available') }}</div>
        <div class="text-h3 font-heading mt-1 text-success">{{ number_format($stats['available_units']) }}</div>
    </div>
</div>

<!-- Filters -->
<div class="mb-6">
    <form method="GET" class="flex flex-col sm:flex-row gap-3 sm:gap-4 flex-wrap items-stretch sm:items-end">
        <div class="w-full sm:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by SKU, name, barcode...') }}" class="input w-full sm:w-64">
        </div>
        <div class="w-full sm:w-auto">
            <select name="stock_status" onchange="this.form.submit()" class="input w-full sm:w-auto">
                <option value="">{{ __('All products') }}</option>
                <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>{{ __('In stock') }}</option>
                <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>{{ __('Low (≤10)') }}</option>
                <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>{{ __('Out of stock') }}</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary min-h-[44px]">
            {{ __('Search') }}
        </button>
        @if(request()->hasAny(['search', 'stock_status']))
            <a href="{{ route('manager.inventory.index') }}" class="btn btn-secondary min-h-[44px] text-center">
                {{ __('Reset') }}
            </a>
        @endif
    </form>
</div>

<!-- Inventory Table -->
<div class="bg-white rounded-card border border-brand-border">
    <div class="table-responsive relative">
        <table class="w-full responsive-table">
            <thead class="bg-bg-soft">
                <tr>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">SKU</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">{{ __('Name') }}</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">{{ __('Barcode') }}</th>
                    <th class="px-6 py-3 text-right text-body-s font-semibold text-text-muted">{{ __('Total') }}</th>
                    <th class="px-6 py-3 text-right text-body-s font-semibold text-text-muted">{{ __('Reserved') }}</th>
                    <th class="px-6 py-3 text-right text-body-s font-semibold text-text-muted">{{ __('Available') }}</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-border">
                @forelse($inventory as $item)
                @php
                    $available = $item->qty_total - $item->qty_reserved;
                    $stockClass = $item->qty_total == 0 ? 'text-error' : ($item->qty_total <= 10 ? 'text-warning' : 'text-success');
                @endphp
                <tr class="hover:bg-bg-soft">
                    <td class="px-6 py-4 text-body-s font-mono" data-label="SKU">{{ $item->sku->sku ?? '-' }}</td>
                    <td class="px-6 py-4" data-label="{{ __('Name') }}">
                        <div class="font-medium">{{ $item->sku->name ?? __('No name') }}</div>
                        @if($item->sku->category)
                            <div class="text-body-xs text-text-muted">{{ $item->sku->category }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-body-s font-mono" data-label="{{ __('Barcode') }}">{{ $item->sku->barcode ?? '-' }}</td>
                    <td class="px-6 py-4 text-right font-semibold {{ $stockClass }}" data-label="{{ __('Total') }}">{{ number_format($item->qty_total) }}</td>
                    <td class="px-6 py-4 text-right text-text-muted" data-label="{{ __('Reserved') }}">{{ number_format($item->qty_reserved) }}</td>
                    <td class="px-6 py-4 text-right font-semibold" data-label="{{ __('Available') }}">{{ number_format($available) }}</td>
                    <td class="px-6 py-4">
                        <button onclick="openAdjustModal({{ $item->id }}, '{{ $item->sku->name ?? $item->sku->sku }}', {{ $item->qty_total }})" class="text-brand hover:underline text-body-s">
                            {{ __('Adjustment') }}
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-text-muted">{{ __('No products') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($inventory->hasPages())
    <div class="p-4 border-t border-brand-border">
        {{ $inventory->links() }}
    </div>
    @endif
</div>

<!-- Adjustment Modal -->
<div id="adjustModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-card p-6 max-w-md w-full mx-4">
        <h3 class="text-h4 font-heading mb-4">{{ __('Stock adjustment') }}</h3>
        <p class="text-body-s text-text-muted mb-4">
            <span id="adjustSkuName"></span> ({{ __('current stock') }}: <strong id="adjustCurrentQty"></strong>)
        </p>
        <form id="adjustForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-body-s font-semibold mb-2">{{ __('Adjustment type') }}</label>
                <select name="adjustment_type" class="input w-full" required>
                    <option value="add">{{ __('Add') }}</option>
                    <option value="subtract">{{ __('Subtract') }}</option>
                    <option value="set">{{ __('Set value') }}</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-body-s font-semibold mb-2">{{ __('Quantity') }}</label>
                <input type="number" name="quantity" class="input w-full" min="0" required placeholder="0">
            </div>
            <div class="mb-6">
                <label class="block text-body-s font-semibold mb-2">{{ __('Reason') }} *</label>
                <input type="text" name="reason" class="input w-full" required placeholder="{{ __('Inventory check, regrading, defect...') }}">
            </div>
            <div class="flex flex-col-reverse sm:flex-row gap-3 sm:gap-4">
                <button type="button" onclick="closeAdjustModal()" class="btn btn-secondary min-h-[44px]">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="btn btn-primary min-h-[44px]">
                    {{ __('Save') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openAdjustModal(inventoryId, skuName, currentQty) {
    document.getElementById('adjustModal').classList.remove('hidden');
    document.getElementById('adjustModal').classList.add('flex');
    document.getElementById('adjustSkuName').textContent = skuName;
    document.getElementById('adjustCurrentQty').textContent = currentQty;
    document.getElementById('adjustForm').action = '/manager/inventory/' + inventoryId + '/adjust';
}

function closeAdjustModal() {
    document.getElementById('adjustModal').classList.add('hidden');
    document.getElementById('adjustModal').classList.remove('flex');
}

// Close modal on backdrop click
document.getElementById('adjustModal').addEventListener('click', function(e) {
    if (e.target === this) closeAdjustModal();
});
</script>
@endpush
@endsection