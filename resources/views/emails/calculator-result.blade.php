<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RISMENT — Результаты расчёта</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 5px 0;
            font-size: 28px;
        }
        .header p {
            margin: 0;
            opacity: 0.9;
            font-size: 15px;
        }
        .content {
            padding: 30px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #f0f0f0;
        }
        .usage-grid {
            display: table;
            width: 100%;
        }
        .usage-row {
            display: table-row;
        }
        .usage-label, .usage-value {
            display: table-cell;
            padding: 6px 0;
        }
        .usage-label {
            color: #666;
        }
        .usage-value {
            text-align: right;
            font-weight: 600;
        }
        .recommendation-box {
            background: linear-gradient(135deg, #f0fff4 0%, #e6ffed 100%);
            border: 2px solid #38a169;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .recommendation-badge {
            display: inline-block;
            background: #38a169;
            color: white;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .recommendation-plan {
            font-size: 22px;
            font-weight: 700;
            color: #667eea;
            margin: 8px 0;
        }
        .recommendation-price {
            font-size: 28px;
            font-weight: 700;
            color: #38a169;
        }
        .recommendation-price span {
            font-size: 14px;
            font-weight: 400;
        }
        .savings-box {
            background: white;
            border-radius: 6px;
            padding: 10px;
            margin-top: 12px;
            display: inline-block;
        }
        .savings-box .amount {
            color: #38a169;
            font-weight: 600;
        }
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
        }
        .breakdown-table td {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .breakdown-table td:last-child {
            text-align: right;
            font-weight: 600;
        }
        .breakdown-total td {
            border-top: 2px solid #333;
            border-bottom: none;
            font-weight: 700;
            font-size: 16px;
            padding-top: 12px;
        }
        .breakdown-total td:last-child {
            color: #667eea;
        }
        .cta {
            text-align: center;
            margin: 25px 0;
        }
        .btn {
            display: inline-block;
            padding: 14px 32px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
        }
        .footer {
            text-align: center;
            padding: 20px 30px;
            color: #666;
            font-size: 13px;
            border-top: 1px solid #eee;
        }
        .note {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 12px 16px;
            font-size: 13px;
            color: #666;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>RISMENT</h1>
            <p>Результаты расчёта стоимости фулфилмента</p>
        </div>

        <div class="content">
            {{-- Input Summary --}}
            <div class="section">
                <div class="section-title">Ваши объёмы в месяц</div>
                <div class="usage-grid">
                    <div class="usage-row">
                        <div class="usage-label">Всего FBS отправлений:</div>
                        <div class="usage-value">{{ $result['usage']['total_shipments'] }} шт</div>
                    </div>
                    @if(($result['usage']['micro_count'] ?? 0) > 0)
                    <div class="usage-row">
                        <div class="usage-label">&nbsp;&nbsp;MICRO:</div>
                        <div class="usage-value">{{ $result['usage']['micro_count'] }} шт</div>
                    </div>
                    @endif
                    @if($result['usage']['mgt_count'] > 0)
                    <div class="usage-row">
                        <div class="usage-label">&nbsp;&nbsp;МГТ:</div>
                        <div class="usage-value">{{ $result['usage']['mgt_count'] }} шт</div>
                    </div>
                    @endif
                    @if($result['usage']['sgt_count'] > 0)
                    <div class="usage-row">
                        <div class="usage-label">&nbsp;&nbsp;СГТ:</div>
                        <div class="usage-value">{{ $result['usage']['sgt_count'] }} шт</div>
                    </div>
                    @endif
                    @if($result['usage']['kgt_count'] > 0)
                    <div class="usage-row">
                        <div class="usage-label">&nbsp;&nbsp;КГТ:</div>
                        <div class="usage-value">{{ $result['usage']['kgt_count'] }} шт</div>
                    </div>
                    @endif
                    @if($result['usage']['storage_box_days'] > 0 || $result['usage']['storage_bag_days'] > 0)
                    <div class="usage-row">
                        <div class="usage-label">Хранение:</div>
                        <div class="usage-value">{{ $result['usage']['storage_box_days'] }} короб-дней + {{ $result['usage']['storage_bag_days'] }} мешок-дней</div>
                    </div>
                    @endif
                    @if($result['usage']['inbound_boxes'] > 0)
                    <div class="usage-row">
                        <div class="usage-label">Приёмка:</div>
                        <div class="usage-value">{{ $result['usage']['inbound_boxes'] }} коробов</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Recommendation --}}
            @php
                $recommended = $result['comparison']['recommended'];
                $isPackage = $recommended['type'] === 'plan';
            @endphp

            <div class="recommendation-box">
                <div class="recommendation-badge">РЕКОМЕНДАЦИЯ</div>

                @if($isPackage)
                    <div class="recommendation-plan">Пакет {{ $recommended['plan']->getName() }}</div>
                @else
                    <div class="recommendation-plan">Оплата по факту</div>
                @endif

                <div class="recommendation-price">
                    {{ number_format($recommended['total'], 0, '', ' ') }} <span>сум/мес</span>
                </div>

                @if($isPackage && $recommended['savings_vs_per_unit'] > 0)
                <div class="savings-box">
                    Экономия: <span class="amount">-{{ number_format($recommended['savings_vs_per_unit'], 0, '', ' ') }} сум/мес</span>
                    ({{ number_format($recommended['savings_percent'], 1) }}%)
                </div>
                @endif
            </div>

            {{-- Cost Breakdown --}}
            <div class="section">
                <div class="section-title">Детализация расчёта</div>
                <table class="breakdown-table">
                    @if($isPackage)
                        <tr>
                            <td>Абонплата пакета {{ $recommended['plan']->getName() }}</td>
                            <td>{{ number_format($recommended['breakdown']['monthly_fee'], 0, '', ' ') }} сум</td>
                        </tr>
                        @if($recommended['breakdown']['overage']['total'] > 0)
                            @if(isset($recommended['breakdown']['overage']['shipments']) && $recommended['breakdown']['overage']['shipments']['total'] > 0)
                            <tr>
                                <td>Отправления сверх лимита</td>
                                <td>{{ number_format($recommended['breakdown']['overage']['shipments']['total'], 0, '', ' ') }} сум</td>
                            </tr>
                            @endif
                            @if(isset($recommended['breakdown']['overage']['storage']) && $recommended['breakdown']['overage']['storage'] > 0)
                            <tr>
                                <td>Хранение сверх лимита</td>
                                <td>{{ number_format($recommended['breakdown']['overage']['storage'], 0, '', ' ') }} сум</td>
                            </tr>
                            @endif
                            @if(isset($recommended['breakdown']['overage']['inbound']) && $recommended['breakdown']['overage']['inbound'] > 0)
                            <tr>
                                <td>Приёмка сверх лимита</td>
                                <td>{{ number_format($recommended['breakdown']['overage']['inbound'], 0, '', ' ') }} сум</td>
                            </tr>
                            @endif
                        @endif
                    @else
                        @php
                            $perUnitOption = collect($result['comparison']['all_options'])->where('type', 'per_unit')->first();
                        @endphp
                        @if(($perUnitOption['breakdown']['micro']['count'] ?? 0) > 0)
                        <tr>
                            <td>MICRO: {{ $perUnitOption['breakdown']['micro']['count'] }} x {{ number_format($perUnitOption['breakdown']['micro']['rate_per_shipment'], 0, '', ' ') }}</td>
                            <td>{{ number_format($perUnitOption['breakdown']['micro']['total'], 0, '', ' ') }} сум</td>
                        </tr>
                        @endif
                        @if($perUnitOption['breakdown']['mgt']['count'] > 0)
                        <tr>
                            <td>МГТ: {{ $perUnitOption['breakdown']['mgt']['count'] }} x {{ number_format($perUnitOption['breakdown']['mgt']['rate_per_shipment'], 0, '', ' ') }}</td>
                            <td>{{ number_format($perUnitOption['breakdown']['mgt']['total'], 0, '', ' ') }} сум</td>
                        </tr>
                        @endif
                        @if($perUnitOption['breakdown']['sgt']['count'] > 0)
                        <tr>
                            <td>СГТ: {{ $perUnitOption['breakdown']['sgt']['count'] }} x {{ number_format($perUnitOption['breakdown']['sgt']['rate_per_shipment'], 0, '', ' ') }}</td>
                            <td>{{ number_format($perUnitOption['breakdown']['sgt']['total'], 0, '', ' ') }} сум</td>
                        </tr>
                        @endif
                        @if($perUnitOption['breakdown']['kgt']['count'] > 0)
                        <tr>
                            <td>КГТ: {{ $perUnitOption['breakdown']['kgt']['count'] }} x {{ number_format($perUnitOption['breakdown']['kgt']['rate_per_shipment'], 0, '', ' ') }}</td>
                            <td>{{ number_format($perUnitOption['breakdown']['kgt']['total'], 0, '', ' ') }} сум</td>
                        </tr>
                        @endif
                        @if($result['usage']['storage_box_days'] > 0 || $result['usage']['storage_bag_days'] > 0)
                        <tr>
                            <td>Хранение</td>
                            <td>{{ number_format($perUnitOption['breakdown']['storage']['cost'], 0, '', ' ') }} сум</td>
                        </tr>
                        @endif
                        @if($result['usage']['inbound_boxes'] > 0)
                        <tr>
                            <td>Приёмка: {{ $result['usage']['inbound_boxes'] }} коробов</td>
                            <td>{{ number_format($perUnitOption['breakdown']['inbound']['cost'], 0, '', ' ') }} сум</td>
                        </tr>
                        @endif
                    @endif
                    <tr class="breakdown-total">
                        <td>Итого:</td>
                        <td>{{ number_format($recommended['total'], 0, '', ' ') }} сум/мес</td>
                    </tr>
                </table>
            </div>

            {{-- Comparison Summary --}}
            <div class="section">
                <div class="section-title">Сравнение вариантов</div>
                <table class="breakdown-table">
                    @foreach($result['comparison']['all_options'] as $option)
                    <tr>
                        <td>
                            @if($option['type'] === 'plan')
                                {{ $option['plan']->getName() }}
                            @else
                                Без пакета
                            @endif
                            @if($option['type'] === $recommended['type'] && ($option['type'] === 'per_unit' || (isset($option['plan']) && isset($recommended['plan']) && $option['plan']->code === $recommended['plan']->code)))
                                &#11088;
                            @endif
                        </td>
                        <td>{{ number_format($option['total'], 0, '', ' ') }} сум</td>
                    </tr>
                    @endforeach
                </table>
            </div>

            {{-- CTA --}}
            <div class="cta">
                <a href="{{ url(app()->getLocale() . '/calculator') }}" class="btn">Пересчитать на сайте</a>
            </div>

            <div class="note">
                * Цены указаны за базовые услуги FBS, хранение и приёмку<br>
                * DBS и FBO услуги рассчитываются отдельно<br>
                * Окончательная стоимость зависит от фактического использования
            </div>
        </div>

        <div class="footer">
            <p style="margin: 0 0 5px 0;"><strong>RISMENT</strong> — Профессиональный фулфилмент для маркетплейсов Узбекистана</p>
            <p style="margin: 0;">info@risment.uz | +998 (71) 123-45-67</p>
            <p style="margin: 10px 0 0 0; font-size: 11px; color: #999;">Это автоматическое письмо. Пожалуйста, не отвечайте на него.</p>
        </div>
    </div>
</body>
</html>
