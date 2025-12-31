@php
$showcaseData = [
    'selectedPlan' => 'GROWTH',
    'prices' => [
        'CONTROL' => ['uzum' => 1490000, 'complex' => 1990000],
        'GROWTH' => ['uzum' => 1890000, 'complex' => 2390000],
        'SCALE' => ['uzum' => 2690000, 'complex' => 3690000],
    ],
];
@endphp

<div x-data='@json($showcaseData)' 
     x-init="
        basePrice = () => prices[selectedPlan].uzum + (prices[selectedPlan].complex * 3);
        discount = () => Math.round(basePrice() * 0.18);
        total = () => basePrice() - discount();
     "
     class="card bg-gradient-to-br from-success/10 to-bg-soft border-2 border-success/30">
    
    <div class="text-center mb-6">
        <h3 class="text-h2 font-heading mb-2">Выгода при подключении всех 4 площадок</h3>
        <p class="text-body-m text-text-muted">Максимальная экономия 18% при работе со всеми маркетплейсами</p>
    </div>
    
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="text-center p-4 bg-white rounded-btn">
            <div class="text-h3 text-success mb-1">−7%</div>
            <div class="text-body-s text-text-muted">2 площадки</div>
        </div>
        <div class="text-center p-4 bg-white rounded-btn">
            <div class="text-h3 text-success mb-1">−12%</div>
            <div class="text-body-s text-text-muted">3 площадки</div>
        </div>
        <div class="text-center p-4 bg-white rounded-btn border-2 border-success">
            <div class="text-h3 text-success mb-1">−18%</div>
            <div class="text-body-s text-text-muted font-semibold">Все 4</div>
        </div>
    </div>
    
    <div class="bg-white rounded-btn p-6">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-6">
            <label class="font-semibold whitespace-nowrap">Выберите пакет:</label>
            <select x-model="selectedPlan" class="select flex-1 sm:flex-initial">
                <option value="CONTROL">Control</option>
                <option value="GROWTH">Growth ⭐</option>
                <option value="SCALE">Scale</option>
            </select>
        </div>
        
        <div class="space-y-3 text-body-m">
            <div class="flex justify-between text-text-muted">
                <span>Uzum (1 шт):</span>
                <span x-text="prices[selectedPlan].uzum.toLocaleString('ru-RU') + ' сум'"></span>
            </div>
            <div class="flex justify-between text-text-muted">
                <span>WB + Ozon + Yandex (3 шт):</span>
                <span x-text="(prices[selectedPlan].complex * 3).toLocaleString('ru-RU') + ' сум'"></span>
            </div>
            <div class="border-t border-brand-border pt-3 flex justify-between">
                <span>База без скидки:</span>
                <span class="font-semibold" x-text="basePrice().toLocaleString('ru-RU') + ' сум'"></span>
            </div>
            <div class="flex justify-between text-success font-semibold text-h4">
                <span>Скидка 18%:</span>
                <span x-text="'−' + discount().toLocaleString('ru-RU') + ' сум'"></span>
            </div>
            <div class="border-t-2 border-brand pt-4 flex justify-between items-center">
                <span class="text-h4 font-heading">Итого в месяц:</span>
                <span class="text-h2 text-brand" x-text="total().toLocaleString('ru-RU') + ' сум'"></span>
            </div>
        </div>
        
        <div class="mt-6 p-4 bg-bg-soft rounded-btn text-body-s text-text-muted">
            <div class="font-semibold text-text mb-2">💰 Ваша выгода:</div>
            <div>Экономия <span class="text-success font-semibold" x-text="discount().toLocaleString('ru-RU') + ' сум'"></span> ежемесячно!</div>
        </div>
    </div>
    
    <div class="mt-6 text-center">
        <a href="{{ route('calculators.marketplace', app()->getLocale()) }}" class="btn btn-primary">
            Рассчитать для своих параметров
        </a>
    </div>
</div>
