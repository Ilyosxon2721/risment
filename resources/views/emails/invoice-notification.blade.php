<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Счет #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 700px;
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
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .content {
            padding: 30px;
        }
        .invoice-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
        }
        .label {
            font-weight: 600;
            color: #666;
        }
        .total-row {
            border-top: 2px solid #667eea;
            padding-top: 15px;
            margin-top: 15px;
            font-size: 18px;
            font-weight: bold;
        }
        .status {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-draft { background: #e9ecef; color: #495057; }
        .status-sent { background: #cfe2ff; color: #084298; }
        .status-paid { background: #d1e7dd; color: #0f5132; }
        .status-overdue { background: #f8d7da; color: #842029; }
        .btn {
            display: inline-block;
            padding: 14px 28px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            padding: 20px 30px;
            color: #666;
            font-size: 14px;
            border-top: 1px solid #eee;
        }
        .payment-info {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💰 Счет на оплату</h1>
            <p style="margin: 0; opacity: 0.9;">{{ $invoice->invoice_number }}</p>
        </div>
        
        <div class="content">
            <p>Уважаемый клиент!</p>
            
            <p>Выставлен счет на оплату услуг RISMENT за {{ $invoice->created_at->format('F Y') }}.</p>
            
            <div class="invoice-summary">
                <div class="summary-row">
                    <span class="label">Номер счета:</span>
                    <span>{{ $invoice->invoice_number }}</span>
                </div>
                <div class="summary-row">
                    <span class="label">Дата выставления:</span>
                    <span>{{ $invoice->issue_date ? $invoice->issue_date->format('d.m.Y') : $invoice->created_at->format('d.m.Y') }}</span>
                </div>
                @if($invoice->due_date)
                <div class="summary-row">
                    <span class="label">Срок оплаты:</span>
                    <span>{{ $invoice->due_date->format('d.m.Y') }}</span>
                </div>
                @endif
                <div class="summary-row">
                    <span class="label">Статус:</span>
                    <span class="status status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
                </div>
                
                <div class="summary-row total-row">
                    <span>Итого к оплате:</span>
                    <span style="color: #667eea; font-size: 24px;">{{ number_format($invoice->total, 0, '', ' ') }} сум</span>
                </div>
                
                @if($invoice->paid > 0)
                <div class="summary-row" style="color: #28a745;">
                    <span class="label">Оплачено:</span>
                    <span>{{ number_format($invoice->paid, 0, '', ' ') }} сум</span>
                </div>
                <div class="summary-row" style="font-weight: bold;">
                    <span class="label">Остаток:</span>
                    <span>{{ number_format($invoice->total - $invoice->paid, 0, '', ' ') }} сум</span>
                </div>
                @endif
            </div>
            
            @if($invoice->status !== 'paid')
            <div class="payment-info">
                <strong>⚠️ Важно:</strong> Пожалуйста, произведите оплату до {{ $invoice->due_date ? $invoice->due_date->format('d.m.Y') : 'указанного срока' }}.
            </div>
            @endif
            
            <div style="text-align: center;">
                <a href="{{ url('/cabinet/finance/invoices/' . $invoice->id) }}" class="btn">Просмотреть счет</a>
            </div>
            
            <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 6px;">
                <h3 style="margin-top: 0; color: #667eea;">Реквизиты для оплаты:</h3>
                <p style="margin: 5px 0; font-size: 14px;">
                    <strong>Получатель:</strong> ООО "RISMENT"<br>
                    <strong>ИНН:</strong> 123456789<br>
                    <strong>Банк:</strong> АКБ "Ipak Yo'li"<br>
                    <strong>Расчетный счет:</strong> 12345678901234567890<br>
                    <strong>МФО:</strong> 00123
                </p>
                <p style="margin-top: 15px; font-size: 13px; color: #666;">
                    <em>В назначении платежа укажите: "Оплата по счету {{ $invoice->invoice_number }}"</em>
                </p>
            </div>
            
            <p style="margin-top: 30px; color: #666; font-size: 14px;">
                Если у вас есть вопросы по счету, свяжитесь с нашим отделом продаж:<br>
                📧 billing@risment.uz<br>
                📞 +998 (71) 123-45-67
            </p>
        </div>
        
        <div class="footer">
            <p>С уважением,<br><strong>Команда RISMENT</strong></p>
            <p style="margin: 10px 0 0 0; font-size: 12px;">Это автоматическое уведомление</p>
        </div>
    </div>
</body>
</html>
