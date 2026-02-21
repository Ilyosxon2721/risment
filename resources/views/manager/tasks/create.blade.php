@extends('manager.layout')

@section('title', 'Добавить задачу')

@section('content')
<div class="mb-8">
    <h2 class="text-h2 font-heading">Добавить задачу</h2>
    <p class="text-text-muted mt-1">Зафиксируйте выполненную складскую операцию</p>
</div>

<div class="bg-white rounded-card border border-brand-border p-6 max-w-2xl">
    <form method="POST" action="{{ route('manager.tasks.store') }}">
        @csrf

        <!-- Task Type -->
        <div class="mb-6">
            <label class="block text-body-s font-semibold mb-2">Тип задачи *</label>
            <select name="task_type" id="task_type" class="input w-full" required onchange="toggleFields()">
                <option value="">Выберите тип</option>
                <option value="inbound" {{ old('task_type') === 'inbound' ? 'selected' : '' }}>Приёмка</option>
                <option value="pickpack" {{ old('task_type') === 'pickpack' ? 'selected' : '' }}>Сборка</option>
                <option value="delivery" {{ old('task_type') === 'delivery' ? 'selected' : '' }}>Отгрузка</option>
                <option value="storage" {{ old('task_type') === 'storage' ? 'selected' : '' }}>Хранение</option>
                <option value="return" {{ old('task_type') === 'return' ? 'selected' : '' }}>Возврат</option>
            </select>
            @error('task_type') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Task Date -->
        <div class="mb-6">
            <label class="block text-body-s font-semibold mb-2">Дата выполнения *</label>
            <input type="date" name="task_date" class="input w-full" value="{{ old('task_date', date('Y-m-d')) }}" required>
            @error('task_date') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Inbound Fields -->
        <div id="fields_inbound" class="hidden space-y-4 mb-6 p-4 bg-bg-soft rounded-card">
            <h4 class="font-semibold">Данные приёмки</h4>
            <div>
                <label class="block text-body-s font-semibold mb-2">Количество коробок *</label>
                <input type="number" name="boxes_count" class="input w-full" min="1" value="{{ old('boxes_count') }}" placeholder="10">
                @error('boxes_count') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-body-s font-semibold mb-2">Номер поставки</label>
                <input type="text" name="reference" class="input w-full" value="{{ old('reference') }}" placeholder="INB-001">
            </div>
        </div>

        <!-- PickPack / Delivery Fields -->
        <div id="fields_shipment" class="hidden space-y-4 mb-6 p-4 bg-bg-soft rounded-card">
            <h4 class="font-semibold">Выбор заказа</h4>
            <div>
                <label class="block text-body-s font-semibold mb-2">Заказ (ShipmentFbo)</label>
                <select name="shipment_id" class="input w-full">
                    <option value="">Выберите заказ</option>
                    @foreach($shipments as $shipment)
                        <option value="{{ $shipment->id }}" {{ old('shipment_id') == $shipment->id ? 'selected' : '' }}>
                            #{{ $shipment->id }} — {{ $shipment->marketplace }} — {{ $shipment->status }} ({{ $shipment->created_at->format('d.m.Y') }})
                        </option>
                    @endforeach
                </select>
                @error('shipment_id') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Storage Fields -->
        <div id="fields_storage" class="hidden space-y-4 mb-6 p-4 bg-bg-soft rounded-card">
            <h4 class="font-semibold">Данные хранения</h4>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-body-s font-semibold mb-2">Коробки</label>
                    <input type="number" name="storage_boxes" class="input w-full" min="0" value="{{ old('storage_boxes', 0) }}">
                    @error('storage_boxes') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-body-s font-semibold mb-2">Мешки</label>
                    <input type="number" name="storage_bags" class="input w-full" min="0" value="{{ old('storage_bags', 0) }}">
                    @error('storage_bags') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Return Fields -->
        <div id="fields_return" class="hidden space-y-4 mb-6 p-4 bg-bg-soft rounded-card">
            <h4 class="font-semibold">Данные возврата</h4>
            <div>
                <label class="block text-body-s font-semibold mb-2">Количество единиц *</label>
                <input type="number" name="return_qty" class="input w-full" min="1" value="{{ old('return_qty') }}" placeholder="1">
                @error('return_qty') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Comment -->
        <div class="mb-6">
            <label class="block text-body-s font-semibold mb-2">Комментарий</label>
            <textarea name="comment" class="input w-full" rows="3" placeholder="Дополнительная информация...">{{ old('comment') }}</textarea>
            @error('comment') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-4">
            <button type="submit" class="btn-brand px-6 py-3 rounded-btn text-white font-semibold">
                Сохранить задачу
            </button>
            <a href="{{ route('manager.tasks.index') }}" class="px-6 py-3 rounded-btn border border-brand-border hover:bg-bg-soft font-semibold">
                Отмена
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleFields() {
    const type = document.getElementById('task_type').value;
    document.getElementById('fields_inbound').classList.toggle('hidden', type !== 'inbound');
    document.getElementById('fields_shipment').classList.toggle('hidden', type !== 'pickpack' && type !== 'delivery');
    document.getElementById('fields_storage').classList.toggle('hidden', type !== 'storage');
    document.getElementById('fields_return').classList.toggle('hidden', type !== 'return');
}
// Init on page load (for old() values)
document.addEventListener('DOMContentLoaded', toggleFields);
</script>
@endpush
@endsection
