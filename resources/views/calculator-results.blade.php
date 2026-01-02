        @if(isset($result))
        <div class="card bg-bg-soft">
            <h2 class="text-h2 font-heading mb-6 text-center">Расчёт стоимости</h2>
            
            <!-- All Plans Comparison -->
            <div class="mb-8">
                <h3 class="text-h3 font-heading mb-4">Сравнение всех вариантов</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($result['all_plans'] as $comparison)
                        @if($comparison['plan'])
                            <!-- Package Plan -->
                            <div class="p-4 border-2 rounded-btn {{ $result['recommended'] === $comparison['plan']->code ? 'border-success bg-success/5' : 'border-brand-border bg-white' }}">
                                @if($result['recommended'] === $comparison['plan']->code)
                                <div class="inline-block px-2 py-1 bg-success text-white text-body-xs rounded-full mb-2">
                                    ✓ ВЫГОДНЕЕ
                                </div>
                                @endif
                                
                                <div class="text-h4 font-heading mb-2">{{ $comparison['plan']->getName() }}</div>
                                <div class="text-body-s text-text-muted mb-3">
                                    {{ number_format($comparison['plan']->price_month, 0, '', ' ') }} сум
                                    @if($comparison['overage']['total'] > 0)
                                    <span class="text-warning">+ {{ number_format($comparison['overage']['total'], 0, '', ' ') }}</span>
                                    @endif
                                </div>
                                <div class="text-h3 {{ $result['recommended'] === $comparison['plan']->code ? 'text-success' : 'text-brand' }}">
                                    {{ number_format($comparison['total'], 0, '', ' ') }}
                                </div>
                                <div class="text-body-xs text-text-muted">сум/мес</div>
                            </div>
                        @else
                            <!-- Per-Item -->
                            <div class="p-4 border-2 rounded-btn {{ $result['recommended'] === 'per_item' ? 'border-success bg-success/5' : 'border-brand-border bg-white' }}">
                                @if($result['recommended'] === 'per_item')
                                <div class="inline-block px-2 py-1 bg-success text-white text-body-xs rounded-full mb-2">
                                    ✓ ВЫГОДНЕЕ
                                </div>
                                @endif
                                
                                <div class="text-h4 font-heading mb-2">Разовый тариф</div>
                                <div class="text-body-s text-text-muted mb-3">
                                    Без абонплаты, с надбавкой
                                </div>
                                <div class="text-h3 {{ $result['recommended'] === 'per_item' ? 'text-success' : 'text-brand' }}">
                                    {{ number_format($comparison['total'], 0, '', ' ') }}
                                </div>
                                <div class="text-body-xs text-text-muted">сум/мес</div>
                            </div>
                        @endif
                    @endforeach
                </div>
                
                <!-- Savings -->
                @if($result['savings'] > 0)
                <div class="mt-4 p-4 bg-brand/5 rounded-btn text-center">
                    <div class="text-body-m">
                        Экономия с выгодным вариантом: <span class="font-semibold text-brand">{{ number_format($result['savings'], 0, '', ' ') }} сум/мес</span>
                    </div>
                </div>
                @endif
            </div>
            
            <hr class="my-8 border-brand-border">
            
            <!-- Detailed Breakdown -->
            <div>
                <h3 class="text-h3 font-heading mb-4">Детализация разового тарифа</h3>
                
                <div class="bg-white rounded-btn p-6 space-y-3">
                    <div class="flex justify-between items-center">
                        <span>Pick&Pack ({{ $result['usage']['shipments'] }} заказов):</span>
                        <span class="font-semibold">{{ number_format($result['per_item_total']['pick_pack'], 0, '', ' ') }} сум</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span>Доставка FBS {{ strtoupper($result['usage']['delivery_size']) }} ({{ $result['usage']['shipments'] }} × {{ number_format($result['per_item_total']['delivery_rate'], 0, '', ' ') }}):</span>
                        <span class="font-semibold">{{ number_format($result['per_item_total']['delivery'], 0, '', ' ') }} сум</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span>Хранение ({{ $result['usage']['storage_boxes'] }} коробов + {{ $result['usage']['storage_bags'] }} мешков):</span>
                        <span class="font-semibold">{{ number_format($result['per_item_total']['storage'], 0, '', ' ') }} сум</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span>Приёмка ({{ $result['usage']['inbound_boxes'] }} коробов):</span>
                        <span class="font-semibold">{{ number_format($result['per_item_total']['inbound'], 0, '', ' ') }} сум</span>
                    </div>
                    
                    <div class="border-t border-brand-border pt-3 flex justify-between items-center">
                        <span class="text-h4 font-heading">Итого:</span>
                        <span class="text-h3 text-brand">{{ number_format($result['per_item_total']['total'], 0, '', ' ') }}</span>
                    </div>
                </div>
                
                <p class="text-body-s text-text-muted mt-4 text-center">
                    * DBS и FBO доставка не включены в расчёт
                </p>
            </div>
        </div>
        @endif
