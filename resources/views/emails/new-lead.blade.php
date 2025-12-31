<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новая заявка</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .info-block {
            background: white;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        .info-row {
            margin-bottom: 12px;
        }
        .label {
            font-weight: 600;
            color: #667eea;
            margin-bottom: 4px;
        }
        .value {
            color: #333;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 14px;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            background: #28a745;
            color: white;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📩 Новая заявка с сайта</h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;"><span class="badge">{{ $lead->status }}</span></p>
    </div>
    
    <div class="content">
        <div class="info-block">
            <h2 style="margin-top: 0;">Контактная информация</h2>
            
            <div class="info-row">
                <div class="label">👤 Имя:</div>
                <div class="value">{{ $lead->name }}</div>
            </div>
            
            <div class="info-row">
                <div class="label">📞 Телефон:</div>
                <div class="value"><a href="tel:{{ $lead->phone }}">{{ $lead->phone }}</a></div>
            </div>
            
            @if($lead->company_name)
            <div class="info-row">
                <div class="label">🏢 Компания:</div>
                <div class="value">{{ $lead->company_name }}</div>
            </div>
            @endif
        </div>
        
        @if($lead->marketplaces && count($lead->marketplaces) > 0)
        <div class="info-block">
            <h2 style="margin-top: 0;">Маркетплейсы</h2>
            <div class="value">
                @foreach($lead->marketplaces as $marketplace)
                    <span style="display: inline-block; padding: 4px 12px; background: #e3f2fd; color: #1976d2; border-radius: 4px; margin: 2px;">{{ $marketplace }}</span>
                @endforeach
            </div>
        </div>
        @endif
        
        @if($lead->schemes && count($lead->schemes) > 0)
        <div class="info-block">
            <h2 style="margin-top: 0;">Схемы работы</h2>
            <div class="value">
                @foreach($lead->schemes as $scheme)
                    <span style="display: inline-block; padding: 4px 12px; background: #f3e5f5; color: #7b1fa2; border-radius: 4px; margin: 2px;">{{ $scheme }}</span>
                @endforeach
            </div>
        </div>
        @endif
        
        @if($lead->comment)
        <div class="info-block">
            <h2 style="margin-top: 0;">💬 Комментарий</h2>
            <div class="value" style="white-space: pre-wrap;">{{ $lead->comment }}</div>
        </div>
        @endif
        
        <div class="info-block" style="border-left-color: #6c757d;">
            <div class="info-row">
                <div class="label">🌐 Источник:</div>
                <div class="value">{{ $lead->source_page ?? 'Не указан' }}</div>
            </div>
            
            <div class="info-row">
                <div class="label">📅 Дата:</div>
                <div class="value">{{ $lead->created_at->format('d.m.Y H:i') }}</div>
            </div>
        </div>
    </div>
    
    <div class="footer">
        <p>Это автоматическое уведомление с сайта <strong>RISMENT</strong></p>
        <p style="margin: 5px 0 0 0; font-size: 12px;">Пожалуйста, свяжитесь с клиентом в течение 24 часов</p>
    </div>
</body>
</html>
