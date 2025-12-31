<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тикет #{{ $ticket->id }}</title>
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
            background: {{ $actionType === 'closed' ? '#28a745' : ($actionType === 'replied' ? '#17a2b8' : '#667eea') }};
            color: white;
            padding: 30px;
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .ticket-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .info-row {
            margin: 10px 0;
        }
        .label {
            font-weight: 600;
            color: #666;
            display: inline-block;
            min-width: 100px;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-low { background: #d4edda; color: #155724; }
        .badge-medium { background: #fff3cd; color: #856404; }
        .badge-high { background: #f8d7da; color: #721c24; }
        .btn {
            display: inline-block;
            padding: 12px 24px;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                @if($actionType === 'created')
                    🎫 Новый тикет создан
                @elseif($actionType === 'replied')
                    💬 Получен ответ на тикет
                @elseif($actionType === 'closed')
                    ✅ Тикет закрыт
                @endif
            </h1>
            <p style="margin: 0; opacity: 0.9;">Тикет #{{ $ticket->id }}</p>
        </div>
        
        <div class="content">
            @if($actionType === 'created')
                <p>Ваш тикет успешно создан. Наша команда поддержки рассмотрит его в ближайшее время.</p>
            @elseif($actionType === 'replied')
                <p>На ваш тикет получен ответ от нашей команды поддержки.</p>
            @elseif($actionType === 'closed')
                <p>Ваш тикет был закрыт. Если у вас остались вопросы, вы можете создать новый тикет.</p>
            @endif
            
            <div class="ticket-info">
                <div class="info-row">
                    <span class="label">Номер:</span>
                    <span>#{{ $ticket->id }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Тема:</span>
                    <span>{{ $ticket->subject }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Приоритет:</span>
                    <span class="badge badge-{{ $ticket->priority }}">{{ ucfirst($ticket->priority) }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Статус:</span>
                    <span>{{ ucfirst($ticket->status) }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Создан:</span>
                    <span>{{ $ticket->created_at->format('d.m.Y H:i') }}</span>
                </div>
            </div>
            
            @if($actionType !== 'closed')
                <div style="margin: 20px 0;">
                    <strong>Сообщение:</strong>
                    <div style="background: #fff; border-left: 4px solid #667eea; padding: 15px; margin-top: 10px; white-space: pre-wrap;">{{ $ticket->message }}</div>
                </div>
            @endif
            
            <div style="text-align: center;">
                <a href="{{ url('/cabinet/tickets/' . $ticket->id) }}" class="btn">Просмотреть тикет</a>
            </div>
            
            <p style="margin-top: 30px; color: #666; font-size: 14px;">
                <strong>Время ответа:</strong> Мы стразаемся отвечать на тикеты в течение 24 часов.
            </p>
        </div>
        
        <div class="footer">
            <p>С уважением,<br><strong>Команда поддержки RISMENT</strong></p>
            <p style="margin: 10px 0 0 0; font-size: 12px;">Это автоматическое уведомление</p>
        </div>
    </div>
</body>
</html>
