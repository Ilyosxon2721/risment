@extends('layouts.app')

@section('title', __('FAQ') . ' - RISMENT')

@section('content')
<!-- Hero -->
<section class="gradient-brand text-white py-12 sm:py-16">
    <div class="container-risment">
        <h1 class="text-2xl sm:text-h1 font-heading mb-4">{{ __('FAQ') }}</h1>
        <p class="text-body-l">{{ __('Frequently Asked Questions') }}</p>
    </div>
</section>

<!-- FAQ Content -->
<section class="py-16">
    <div class="container-risment max-w-4xl" x-data="faqSearch()">
        {{-- Search --}}
        <div class="mb-6">
            <div class="relative">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-text-muted pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text"
                       x-model="search"
                       placeholder="{{ __('Search FAQ...') }}"
                       class="input pl-12 w-full"
                       @input="filterFaqs()">
            </div>
        </div>

        {{-- Category tabs --}}
        <div class="flex flex-wrap gap-2 mb-8">
            <button @click="setCategory('all')"
                    :class="activeCategory === 'all' ? 'bg-brand text-white' : 'bg-bg-soft text-text-muted hover:bg-brand/10 hover:text-brand'"
                    class="px-4 py-2 rounded-full text-body-s font-semibold transition-colors min-h-[36px]">
                {{ __('All') }}
            </button>
            <template x-for="cat in categories" :key="cat.key">
                <button @click="setCategory(cat.key)"
                        :class="activeCategory === cat.key ? 'bg-brand text-white' : 'bg-bg-soft text-text-muted hover:bg-brand/10 hover:text-brand'"
                        class="px-4 py-2 rounded-full text-body-s font-semibold transition-colors min-h-[36px]"
                        x-text="cat.label">
                </button>
            </template>
        </div>

        {{-- FAQ items --}}
        <div class="space-y-4">
            <template x-for="(faq, index) in filteredFaqs" :key="index">
                <div class="card">
                    <button type="button"
                            class="w-full text-left flex justify-between items-center min-h-[44px]"
                            @click="toggle(faq)">
                        <span class="font-semibold text-body-l pr-4" x-text="faq.q"></span>
                        <svg class="w-6 h-6 text-brand flex-shrink-0 transition-transform duration-300"
                             :class="faq.open ? 'rotate-180' : ''"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="faq.open"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 max-h-0"
                         x-transition:enter-end="opacity-100 max-h-[500px]"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 max-h-[500px]"
                         x-transition:leave-end="opacity-0 max-h-0"
                         class="overflow-hidden">
                        <div class="mt-4 text-body-m text-text-muted border-t border-brand-border pt-4" x-html="faq.a"></div>
                    </div>
                </div>
            </template>
        </div>

        {{-- No results --}}
        <div x-show="filteredFaqs.length === 0" x-transition class="text-center py-12">
            <svg class="w-16 h-16 text-text-muted/40 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-body-l text-text-muted font-semibold">{{ __('No results found') }}</p>
            <p class="text-body-s text-text-muted mt-1">{{ __('Try changing your search query or category') }}</p>
        </div>

        <!-- CTA -->
        <div class="mt-12 text-center card gradient-brand text-white">
            <h3 class="text-h3 font-heading mb-4">{{ __('Still have questions?') }}</h3>
            <p class="text-body-l mb-6 opacity-90">{{ __('Contact us for detailed consultation') }}</p>
            <a href="{{ route('contacts', ['locale' => app()->getLocale()]) }}" class="btn bg-white text-brand hover:bg-white/90">
                {{ __('Contact Us') }}
            </a>
        </div>
    </div>
</section>

<script>
function faqSearch() {
    @php
        $categories_map = [
            'general'   => __('General'),
            'pricing'   => __('Pricing & Plans'),
            'shipments' => __('Shipments'),
            'payment'   => __('Payment & Returns'),
            'technical' => __('Technical'),
        ];

        $faqs_ru = [
            ['q' => 'Что такое фулфилмент и чем RISMENT отличается от обычного склада?', 'a' => 'Фулфилмент — это не просто хранение. RISMENT принимает товар, ведёт учёт, собирает и упаковывает заказы, делает маркировку и отгружает на маркетплейсы или покупателям. Вы получаете процесс "под ключ" и прозрачные тарифы.', 'cat' => 'general'],
            ['q' => 'С какими маркетплейсами вы работаете?', 'a' => 'Мы работаем с Uzum Market, Wildberries, Ozon и Yandex Market. По запросу подключаем новые площадки.', 'cat' => 'general'],
            ['q' => 'Какие схемы вы поддерживаете: FBO, FBS, DBS?', 'a' => '<strong>FBO</strong> — вы отгружаете партии на склад маркетплейса через нас.<br><strong>FBS</strong> — мы собираем и упаковываем заказы, доставка обычно выполняется маркетплейсом.<br><strong>DBS</strong> — доставка выполняется продавцом (может быть через RISMENT по договорённости).', 'cat' => 'shipments'],
            ['q' => 'Что такое EDBS и почему у разных площадок это разное?', 'a' => 'EDBS — термин, который трактуется по-разному у разных маркетплейсов. Мы уточняем схему под конкретную площадку и фиксируем регламент в договоре, чтобы не было сюрпризов.', 'cat' => 'shipments'],
            ['q' => 'Как рассчитывается "мелкий/средний/крупный" товар (МГТ/СГТ/КГТ)?', 'a' => 'Категория определяется по сумме трёх сторон товара в упаковке: <strong>Д + Ш + В</strong> (см):<br>• <strong>МГТ:</strong> ≤ 60 см<br>• <strong>СГТ:</strong> 60–170 см<br>• <strong>КГТ:</strong> > 170 см<br>Если в заказе несколько товаров — категория считается по <strong>самому крупному</strong>.', 'cat' => 'pricing'],
            ['q' => 'Вы делаете маркировку и подготовку поставок на склад маркетплейса?', 'a' => 'Да. Мы выполняем сортировку, упаковку, маркировку товара и коробов, формируем партии и подготавливаем документы/реестры.', 'cat' => 'shipments'],
            ['q' => 'Делаете ли вы сборку заказов (Pick&Pack) для FBS/DBS?', 'a' => 'Да. Мы собираем заказ, упаковываем, при необходимости добавляем доп.защиту (хрупкое/стретч/сейф-пакет) и передаём на отгрузку по схеме.', 'cat' => 'shipments'],
            ['q' => 'Есть ли минимальные объёмы для начала работы?', 'a' => 'Минимальный объём не обязателен, но на небольших объёмах обычно выгоднее стартовать с базового пакета и понятной операционки. Менеджер поможет подобрать формат.', 'cat' => 'pricing'],
            ['q' => 'Как быстро вы обрабатываете заказы?', 'a' => 'Срок обработки зависит от нагрузки и требований к упаковке. Для большинства сценариев мы работаем в режиме "день-в-день" или "на следующий день". Конкретные SLA фиксируются в регламенте.', 'cat' => 'shipments'],
            ['q' => 'Можно ли привезти товар без штрихкодов/артикулов?', 'a' => 'Можно, но это замедляет приёмку и повышает риск ошибок. Рекомендуем поставки с баркодами и корректными названиями SKU. Если баркодов нет — мы можем помочь, но это отдельная услуга.', 'cat' => 'technical'],
            ['q' => 'Как вы ведёте учёт остатков?', 'a' => 'Остатки ведутся по SKU/штрихкоду с привязкой к месту хранения. Клиент видит данные в личном кабинете (если подключено).', 'cat' => 'technical'],
            ['q' => 'Что делать, если пришёл брак или возврат?', 'a' => 'Мы принимаем возврат, проводим проверку (по регламенту), делаем фотофиксацию, переупаковываем или выводим в брак/списание по согласованию.', 'cat' => 'payment'],
            ['q' => 'Кто отвечает за ошибки комплектации?', 'a' => 'Если ошибка возникла на стороне RISMENT — мы разбираем инцидент по трекингу/фото и компенсируем по условиям договора. Если ошибка из-за неверных данных клиента (SKU, баркоды, инструкции) — ответственность фиксируется регламентом.', 'cat' => 'payment'],
            ['q' => 'Вы работаете только в Коканде?', 'a' => 'Склад расположен в Коканде. Отгрузки на склады маркетплейсов и логистика выполняются по согласованному графику и условиям.', 'cat' => 'general'],
            ['q' => 'Как начать работу с RISMENT?', 'a' => 'Оставьте заявку на сайте. Мы уточним товары, схемы (FBO/FBS/DBS), объёмы и подготовим договор, тарифный план и регламент.', 'cat' => 'general'],
        ];

        $faqs_uz = [
            ['q' => 'Fulfillment nima va RISMENT oddiy ombordan nimasi bilan farq qiladi?', 'a' => 'Fulfillment — bu faqat saqlash emas. RISMENT tovarni qabul qiladi, hisobini yuritadi, buyurtmalarni yig\'adi va qadoqlaydi, markirovka qiladi hamda marketplace omboriga yoki mijozga jo\'natadi. Jarayon "kalit topshirish" ko\'rinishida bo\'ladi.', 'cat' => 'general'],
            ['q' => 'Qaysi marketplace\'lar bilan ishlaysiz?', 'a' => 'Uzum Market, Wildberries, Ozon va Yandex Market bilan ishlaymiz. So\'rov bo\'yicha boshqa platformalarni ham ulash mumkin.', 'cat' => 'general'],
            ['q' => 'Qaysi sxemalar: FBO, FBS, DBS?', 'a' => '<strong>FBO</strong> — partiya ko\'rinishida marketplace omboriga jo\'natish (RISMENT orqali).<br><strong>FBS</strong> — buyurtmani yig\'ish va qadoqlash, yetkazib berish odatda marketplace tomoni.<br><strong>DBS</strong> — yetkazib berishni sotuvchi bajaradi (kelishuvga ko\'ra RISMENT orqali ham bo\'lishi mumkin).', 'cat' => 'shipments'],
            ['q' => 'EDBS nima va nega turli platformalarda turlicha?', 'a' => 'EDBS atamasi marketplace\'ga qarab boshqacha ishlatiladi. Biz har bir platforma bo\'yicha sxemani aniqlab, reglamеntni shartnomada aniq yozib qo\'yamiz.', 'cat' => 'shipments'],
            ['q' => '"Kichik/o\'rta/katta" (MGT/SGT/KGT) qanday aniqlanadi?', 'a' => 'Kategoriya qadoqdagi o\'lchamlar yig\'indisi bo\'yicha: <strong>U + E + B</strong> (sm):<br>• <strong>MGT:</strong> ≤ 60 sm<br>• <strong>SGT:</strong> 60–170 sm<br>• <strong>KGT:</strong> > 170 sm<br>Agar buyurtmada bir nechta tovar bo\'lsa — kategoriya <strong>eng kattasi</strong> bo\'yicha olinadi.', 'cat' => 'pricing'],
            ['q' => 'Marketplace omboriga jo\'natish uchun markirovka va partiya tayyorlaysizmi?', 'a' => 'Ha. Saralash, qadoqlash, tovar va korobkalarni markirovka qilish, partiya shakllantirish hamda hujjatlar/reestrlarni tayyorlashni qilamiz.', 'cat' => 'shipments'],
            ['q' => 'FBS/DBS uchun buyurtma yig\'ish (Pick&Pack) bormi?', 'a' => 'Ha. Buyurtma yig\'iladi, qadoqlanadi, kerak bo\'lsa qo\'shimcha himoya (mo\'rt/strech/safe-paket) qo\'shiladi va sxema bo\'yicha jo\'natishga tayyorlanadi.', 'cat' => 'shipments'],
            ['q' => 'Ishni boshlash uchun minimal hajm kerakmi?', 'a' => 'Majburiy minimal hajm yo\'q. Lekin kichik hajmlarda bazaviy paket va aniq operatsiya bilan boshlash foydaliroq bo\'ladi. Menejer mos variantni tavsiya qiladi.', 'cat' => 'pricing'],
            ['q' => 'Buyurtmalarni qanchada tayyorlaysiz?', 'a' => 'Muddat yuklama va qadoqlash talablarga bog\'liq. Ko\'p holatda "shu kuni" yoki "ertasi kuni" rejimida ishlaymiz. Aniq SLA reglamеntda belgilanadi.', 'cat' => 'shipments'],
            ['q' => 'Shtrix-kodsiz tovar olib kelish mumkinmi?', 'a' => 'Mumkin, lekin qabul qilish sekinlashadi va xatolik riski oshadi. Shuning uchun SKU nomlari va barcode\'lar tayyor bo\'lishi tavsiya qilinadi. Barcode bo\'lmasa — alohida xizmat sifatida yordam beramiz.', 'cat' => 'technical'],
            ['q' => 'Qoldiq hisobi qanday yuritiladi?', 'a' => 'Qoldiq SKU/shtrix-kod bo\'yicha va saqlash joyi bilan yuritiladi. Ulangan bo\'lsa, mijoz shaxsiy kabinetda ko\'radi.', 'cat' => 'technical'],
            ['q' => 'Brak yoki qaytgan tovar bo\'lsa nima qilasiz?', 'a' => 'Qaytgan tovar qabul qilinadi, reglamеnt bo\'yicha tekshiriladi, foto-fiksatsiya qilinadi. Kelishuvga ko\'ra qayta qadoqlash yoki brak/yo\'q qilish amalga oshiriladi.', 'cat' => 'payment'],
            ['q' => 'Komplektatsiya xatosi uchun kim javob beradi?', 'a' => 'Agar xato RISMENT tomonidan bo\'lsa — treking/foto asosida tekshiramiz va shartnoma bo\'yicha kompensatsiya qilamiz. Agar xato noto\'g\'ri ma\'lumot (SKU, barcode, ko\'rsatma) sabab bo\'lsa — javobgarlik reglamеntda belgilanadi.', 'cat' => 'payment'],
            ['q' => 'Faqat Qo\'qonda ishlaysizmi?', 'a' => 'Ombor Qo\'qonda joylashgan. Marketplace omborlariga jo\'natish va logistika kelishilgan grafik va shartlar bo\'yicha bajariladi.', 'cat' => 'general'],
            ['q' => 'RISMENT bilan qanday boshlash mumkin?', 'a' => 'Saytda ariza qoldiring. Tovar, sxema (FBO/FBS/DBS), hajmni aniqlab, shartnoma, tarif va reglamеntni tayyorlaymiz.', 'cat' => 'general'],
        ];

        $faqs_en = [
            ['q' => 'What is fulfillment and how is RISMENT different from a regular warehouse?', 'a' => 'Fulfillment is more than just storage. RISMENT receives goods, manages inventory, assembles and packs orders, labels them, and ships to marketplaces or buyers. You get a turnkey process with transparent pricing.', 'cat' => 'general'],
            ['q' => 'Which marketplaces do you work with?', 'a' => 'We work with Uzum Market, Wildberries, Ozon, and Yandex Market. Additional platforms can be connected upon request.', 'cat' => 'general'],
            ['q' => 'What schemes do you support: FBO, FBS, DBS?', 'a' => '<strong>FBO</strong> — you ship batches to the marketplace warehouse through us.<br><strong>FBS</strong> — we assemble and pack orders, delivery is usually handled by the marketplace.<br><strong>DBS</strong> — delivery is done by the seller (can be through RISMENT by agreement).', 'cat' => 'shipments'],
            ['q' => 'What is EDBS and why is it different across platforms?', 'a' => 'EDBS is a term interpreted differently by different marketplaces. We clarify the scheme for each specific platform and fix the regulations in the contract to avoid surprises.', 'cat' => 'shipments'],
            ['q' => 'How is "small/medium/large" item (MGT/SGT/LGT) determined?', 'a' => 'The category is determined by the sum of three dimensions of the packaged item: <strong>L + W + H</strong> (cm):<br>• <strong>MGT:</strong> ≤ 60 cm<br>• <strong>SGT:</strong> 60–170 cm<br>• <strong>LGT:</strong> > 170 cm<br>If an order contains multiple items, the category is determined by the <strong>largest one</strong>.', 'cat' => 'pricing'],
            ['q' => 'Do you label and prepare shipments for marketplace warehouses?', 'a' => 'Yes. We sort, pack, label goods and boxes, form batches, and prepare documents/registries.', 'cat' => 'shipments'],
            ['q' => 'Do you do order assembly (Pick&Pack) for FBS/DBS?', 'a' => 'Yes. We assemble the order, pack it, add extra protection if needed (fragile/stretch/security bag), and transfer it for shipping.', 'cat' => 'shipments'],
            ['q' => 'Is there a minimum volume to start?', 'a' => 'No mandatory minimum volume, but for small volumes it\'s usually more cost-effective to start with a basic package. Our manager will help find the right format.', 'cat' => 'pricing'],
            ['q' => 'How quickly do you process orders?', 'a' => 'Processing time depends on workload and packaging requirements. In most cases, we work in "same day" or "next day" mode. Specific SLAs are set in the contract.', 'cat' => 'shipments'],
            ['q' => 'Can I deliver goods without barcodes?', 'a' => 'You can, but it slows down receiving and increases error risk. We recommend shipments with barcodes and correct SKU names. If there are no barcodes, we can help as a separate service.', 'cat' => 'technical'],
            ['q' => 'How do you track inventory?', 'a' => 'Inventory is tracked by SKU/barcode with storage location binding. Clients can view data in their personal dashboard.', 'cat' => 'technical'],
            ['q' => 'What happens with defective or returned items?', 'a' => 'We accept returns, inspect them per regulations, take photos, repack or classify as defective/write-off by agreement.', 'cat' => 'payment'],
            ['q' => 'Who is responsible for picking errors?', 'a' => 'If the error occurred on RISMENT\'s side, we investigate via tracking/photos and compensate per contract terms. If the error is due to incorrect client data (SKU, barcodes, instructions), responsibility is defined in the regulations.', 'cat' => 'payment'],
            ['q' => 'Do you only work in Kokand?', 'a' => 'The warehouse is located in Kokand. Shipments to marketplace warehouses and logistics are carried out according to an agreed schedule and terms.', 'cat' => 'general'],
            ['q' => 'How to start working with RISMENT?', 'a' => 'Leave a request on our website. We will clarify products, schemes (FBO/FBS/DBS), volumes, and prepare a contract, pricing plan, and regulations.', 'cat' => 'general'],
        ];

        $faqs = app()->getLocale() === 'ru' ? $faqs_ru : (app()->getLocale() === 'en' ? $faqs_en : $faqs_uz);
    @endphp

    const allFaqs = @json($faqs).map(f => ({...f, open: false}));

    return {
        search: '',
        activeCategory: 'all',
        allFaqs: allFaqs,
        filteredFaqs: [...allFaqs],
        categories: @json(collect($categories_map)->map(fn($label, $key) => ['key' => $key, 'label' => $label])->values()),

        setCategory(cat) {
            this.activeCategory = cat;
            this.filterFaqs();
        },

        filterFaqs() {
            const q = this.search.toLowerCase().trim();
            const cat = this.activeCategory;

            this.filteredFaqs = this.allFaqs.filter(faq => {
                const matchesCat = cat === 'all' || faq.cat === cat;
                const matchesSearch = !q ||
                    faq.q.toLowerCase().includes(q) ||
                    faq.a.replace(/<[^>]*>/g, '').toLowerCase().includes(q);
                return matchesCat && matchesSearch;
            });
        },

        toggle(faq) {
            faq.open = !faq.open;
        }
    };
}
</script>
@endsection
