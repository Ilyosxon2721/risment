@extends('layouts.app')

@section('title', __('SLA') . ' - RISMENT')

@section('content')
<section class="py-16">
    <div class="container-risment max-w-4xl">
        <h1 class="text-h1 font-heading text-center mb-12">
            @if(app()->getLocale() === 'ru')
                SLA и регламенты
            @else
                SLA va reglamentlar
            @endif
        </h1>
        
        <div class="prose max-w-none">
            <div class="card mb-8">
                <h2 class="text-h3 font-heading mb-4">
                    @if(app()->getLocale() === 'ru')
                        Сроки обработки
                    @else
                        Ishlov berish muddatlari
                    @endif
                </h2>
                <ul class="space-y-3">
                    <li class="flex justify-between items-center p-3 bg-bg-soft rounded-btn">
                        <span>
                            @if(app()->getLocale() === 'ru')
                                Приёмка товара
                            @else
                                Mahsulotni qabul qilish
                            @endif
                        </span>
                        <span class="font-semibold text-brand">
                            @if(app()->getLocale() === 'ru')
                                1-2 рабочих дня
                            @else
                                1-2 ish kuni
                            @endif
                        </span>
                    </li>
                    <li class="flex justify-between items-center p-3 bg-bg-soft rounded-btn">
                        <span>
                            @if(app()->getLocale() === 'ru')
                                Сборка заказа FBS/DBS
                            @else
                                FBS/DBS buyurtmasini yig'ish
                            @endif
                        </span>
                        <span class="font-semibold text-brand">
                            @if(app()->getLocale() === 'ru')
                                В день заказа
                            @else
                                Buyurtma kunida
                            @endif
                        </span>
                    </li>
                    <li class="flex justify-between items-center p-3 bg-bg-soft rounded-btn">
                        <span>
                            @if(app()->getLocale() === 'ru')
                                Подготовка поставки FBO
                            @else
                                FBO etkazib berishni tayyorlash
                            @endif
                        </span>
                        <span class="font-semibold text-brand">
                            @if(app()->getLocale() === 'ru')
                                2-3 рабочих дня
                            @else
                                2-3 ish kuni
                            @endif
                        </span>
                    </li>
                </ul>
            </div>
            
            <div class="card mb-8">
                <h2 class="text-h3 font-heading mb-4">
                    @if(app()->getLocale() === 'ru')
                        Требования к упаковке
                    @else
                        Qadoqlash talablari
                    @endif
                </h2>
                <p class="text-body-m text-text-muted mb-4">
                    @if(app()->getLocale() === 'ru')
                        Товар должен быть упакован согласно требованиям маркетплейса:
                    @else
                        Mahsulot marketplace talablariga muvofiq qadoqlangan bo'lishi kerak:
                    @endif
                </p>
                <ul class="list-disc list-inside space-y-2 text-body-m text-text-muted">
                    <li>
                        @if(app()->getLocale() === 'ru')
                            Индивидуальная упаковка для каждой единицы
                        @else
                            Har bir birlik uchun individual qadoqlash
                        @endif
                    </li>
                    <li>
                        @if(app()->getLocale() === 'ru')
                            Защита от повреждений при транспортировке
                        @else
                            Tashish vaqtida shikastlanishdan himoya
                        @endif
                    </li>
                    <li>
                        @if(app()->getLocale() === 'ru')
                            Маркировка со штрих-кодом
                        @else
                            Shtrix-kod bilan markalash
                        @endif
                    </li>
                </ul>
            </div>
            
            <div class="card mb-8">
                <h2 class="text-h3 font-heading mb-4">
                    @if(app()->getLocale() === 'ru')
                        Ответственность
                    @else
                        Mas'uliyat
                    @endif
                </h2>
                <p class="text-body-m text-text-muted mb-4">
                    @if(app()->getLocale() === 'ru')
                        RISMENT несёт ответственность за:
                    @else
                        RISMENT quyidagilar uchun javobgardir:
                    @endif
                </p>
                <ul class="list-disc list-inside space-y-2 text-body-m text-text-muted">
                    <li>
                        @if(app()->getLocale() === 'ru')
                            Сохранность товара на складе
                        @else
                            Ombordagi mahsulot xavfsizligi
                        @endif
                    </li>
                    <li>
                        @if(app()->getLocale() === 'ru')
                            Точность комплектации заказов
                        @else
                            Buyurtmalarni to'plash aniqligi
                        @endif
                    </li>
                    <li>
                        @if(app()->getLocale() === 'ru')
                            Соблюдение сроков SLA
                        @else
                            SLA muddatlariga rioya qilish
                        @endif
                    </li>
                </ul>
            </div>
            
            <div class="card gradient-brand text-white p-8">
                <h3 class="text-h3 font-heading mb-4">
                    @if(app()->getLocale() === 'ru')
                        Особенности EDBS
                    @else
                        EDBS xususiyatlari
                    @endif
                </h3>
                <p class="text-body-m opacity-90">
                    @if(app()->getLocale() === 'ru')
                        Отличаются по площадкам. Wildberries и Uzum имеют разные требования к маркировке и упаковке при работе по схеме EDBS.
                    @else
                        Platformalar bo'yicha farq qiladi. Wildberries va Uzum EDBS sxemasi bo'yicha ishlashda markalash va qadoqlashga turli talablarga ega.
                    @endif
                </p>
            </div>
        </div>
    </div>
</section>
@endsection
