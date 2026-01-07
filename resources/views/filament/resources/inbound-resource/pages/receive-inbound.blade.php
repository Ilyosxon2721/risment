<x-filament-panels::page>
    <div x-data="receivingPage()" x-init="init()">
        {{-- Header with Scanner Controls --}}
        <div class="mb-6 flex justify-between items-center bg-white p-4 rounded-lg shadow-sm border">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $record->reference }}</h2>
                <p class="text-sm text-gray-600 mt-1">{{ $record->company->name }}</p>
            </div>
            
            <div class="flex gap-3">
                <button 
                    type="button"
                    @click="toggleUSBScanner()"
                    x-bind:style="usbScannerActive ? 'background-color: #2563eb !important; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);' : ''"
                    style="background-color: #4b5563; color: white; padding: 0.625rem 1.25rem; border-radius: 0.5rem; font-weight: 600; transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem; border: none; cursor: pointer;">
                    <template x-if="!usbScannerActive">
                        <span style="display: flex; align-items: center; gap: 0.5rem;">
                            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>USB Сканер</span>
                        </span>
                    </template>
                    <template x-if="usbScannerActive">
                        <span style="display: flex; align-items: center; gap: 0.5rem;">
                            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>USB Активен</span>
                        </span>
                    </template>
                </button>
                
                <button 
                    type="button"
                    @click="toggleCameraScanner()"
                    x-bind:style="cameraScannerActive ? 'background-color: #16a34a !important; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);' : ''"
                    style="background-color: #4b5563; color: white; padding: 0.625rem 1.25rem; border-radius: 0.5rem; font-weight: 600; transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem; border: none; cursor: pointer;">
                    <template x-if="!cameraScannerActive">
                        <span style="display: flex; align-items: center; gap: 0.5rem;">
                            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Камера</span>
                        </span>
                    </template>
                    <template x-if="cameraScannerActive">
                        <span style="display: flex; align-items: center; gap: 0.5rem;">
                            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Камера Активна</span>
                        </span>
                    </template>
                </button>
            </div>
        </div>

        {{-- Camera Scanner View (hidden by default) --}}
        <div x-show="cameraScannerActive" x-cloak class="mb-6 bg-gray-100 p-4 rounded-lg">
            <div id="camera-scanner" class="w-full max-w-md mx-auto"></div>
            <p class="text-center mt-2 text-sm text-gray-600">Наведите камеру на штрих-код</p>
        </div>

        {{-- USB Scanner Input (hidden) --}}
        <input 
            x-show="usbScannerActive"
            x-ref="usbScannerInput"
            type="text"
            @input.debounce.500ms="handleUSBScan($event.target.value)"
            placeholder="Отсканируйте товар USB сканером..."
            class="w-full px-4 py-2 border rounded-lg mb-4"
            autofocus
        >

        {{-- Items Table --}}
        <div class="bg-white rounded-lg shadow-md overflow-hidden border">
            <table class="w-full">
                <thead class="bg-gray-100 border-b-2 border-gray-200">
                    <tr>
                        <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Товар</th>
                        <th class="px-4 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">План</th>
                        <th class="px-4 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Факт</th>
                        <th class="px-4 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Разница</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Примечания</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($record->items as $item)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $item->variant->product->title }}</div>
                            <div class="text-sm text-gray-500">{{ $item->variant->variant_name }} ({{ $item->variant->sku_code }})</div>
                        </td>
                        <td class="px-4 py-3 text-center font-medium">{{ $item->qty_planned }}</td>
                        <td class="px-4 py-3">
                            <input 
                                type="number" 
                                wire:model.live="itemsData.{{ $item->id }}.qty_received"
                                class="w-24 px-2 py-1 text-center border rounded"
                                min="0"
                            >
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $diff = ($itemsData[$item->id]['qty_received'] ?? $item->qty_planned) - $item->qty_planned;
                            @endphp
                            <span class="px-2 py-1 rounded text-sm font-medium {{ $diff == 0 ? 'bg-green-100 text-green-800' : ($diff > 0 ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                {{ $diff > 0 ? '+' : '' }}{{ $diff }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <input 
                                type="text" 
                                wire:model="itemsData.{{ $item->id }}.notes"
                                class="w-full px-2 py-1 border rounded text-sm"
                                placeholder="Примечания..."
                            >
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Summary --}}
        <div class="mt-6 bg-blue-50 p-4 rounded-lg">
            <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                    <div class="text-2xl font-bold text-blue-600">{{ $record->items->sum('qty_planned') }}</div>
                    <div class="text-sm text-gray-600">План</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-green-600">{{ collect($itemsData)->sum('qty_received') }}</div>
                    <div class="text-sm text-gray-600">Факт</div>
                </div>
                <div>
                    @php
                        $totalDiff = collect($itemsData)->sum('qty_received') - $record->items->sum('qty_planned');
                    @endphp
                    <div class="text-2xl font-bold {{ $totalDiff == 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $totalDiff > 0 ? '+' : '' }}{{ $totalDiff }}
                    </div>
                    <div class="text-sm text-gray-600">Разница</div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="mt-6 flex justify-end gap-4">
            <button 
                type="button"
                wire:click="saveDraft"
                style="background-color: #9333ea; color: white; padding: 0.75rem 2rem; border-radius: 0.5rem; font-weight: 600; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem; border: none; cursor: pointer;"
                onmouseover="this.style.backgroundColor='#7e22ce'; this.style.boxShadow='0 10px 15px -3px rgb(0 0 0 / 0.1)'"
                onmouseout="this.style.backgroundColor='#9333ea'; this.style.boxShadow='0 4px 6px -1px rgb(0 0 0 / 0.1)'">
                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
                Сохранить черновик
            </button>
            <button 
                type="button"
                wire:click="complete"
                style="background-color: #16a34a; color: white; padding: 0.75rem 2rem; border-radius: 0.5rem; font-weight: 600; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem; border: none; cursor: pointer;"
                onmouseover="this.style.backgroundColor='#15803d'; this.style.boxShadow='0 10px 15px -3px rgb(0 0 0 / 0.1)'"
                onmouseout="this.style.backgroundColor='#16a34a'; this.style.boxShadow='0 4px 6px -1px rgb(0 0 0 / 0.1)'">
                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Завершить приёмку
            </button>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
    function receivingPage() {
        return {
            usbScannerActive: false,
            cameraScannerActive: false,
            html5QrCode: null,
            scannedBuffer: '',
            
            init() {
                console.log('Receiving page initialized');
            },
            
            toggleUSBScanner() {
                this.usbScannerActive = !this.usbScannerActive;
                if (this.usbScannerActive) {
                    this.$nextTick(() => {
                        this.$refs.usbScannerInput?.focus();
                    });
                }
            },
            
            toggleCameraScanner() {
                this.cameraScannerActive = !this.cameraScannerActive;
                
                if (this.cameraScannerActive) {
                    this.startCameraScanner();
                } else {
                    this.stopCameraScanner();
                }
            },
            
            startCameraScanner() {
                this.html5QrCode = new Html5Qrcode("camera-scanner");
                
                this.html5QrCode.start(
                    { facingMode: "environment" },
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    (decodedText) => {
                        this.handleScan(decodedText);
                        // Play success sound
                        this.playBeep();
                    },
                    (error) => {
                        // Silent error handling
                    }
                ).catch(err => {
                    console.error('Camera scanner error:', err);
                    alert('Ошибка доступа к камере. Проверьте разрешения браузера.');
                    this.cameraScannerActive = false;
                });
            },
            
            stopCameraScanner() {
                if (this.html5QrCode) {
                    this.html5QrCode.stop().then(() => {
                        this.html5QrCode.clear();
                    });
                }
            },
            
            handleUSBScan(value) {
                if (value.length > 3) {
                    this.handleScan(value);
                    this.$refs.usbScannerInput.value = '';
                }
            },
            
            handleScan(barcode) {
                // Call Livewire method
                @this.call('scanBarcode', barcode);
            },
            
            playBeep() {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                oscillator.frequency.value = 800;
                oscillator.connect(audioContext.destination);
                oscillator.start();
                setTimeout(() => oscillator.stop(), 100);
            }
        }
    }
    </script>
    @endpush
</x-filament-panels::page>
