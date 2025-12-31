<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добро пожаловать в RISMENT</title>
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
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 32px;
        }
        .content {
            padding: 40px 30px;
        }
        .welcome-text {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .cta {
            text-align: center;
            margin: 30px 0;
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
        .features {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin: 30px 0;
        }
        .features h3 {
            margin-top: 0;
            color: #667eea;
        }
        .feature-item {
            margin: 15px 0;
            padding-left: 30px;
            position: relative;
        }
        .feature-item:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #28a745;
            font-weight: bold;
            font-size: 20px;
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
            <h1>🎉 Добро пожаловать!</h1>
            <p style="margin: 0; opacity: 0.9; font-size: 16px;">Рады видеть вас в RISMENT</p>
        </div>
        
        <div class="content">
            <p class="welcome-text">Здравствуйте, <strong>{{ $user->name }}</strong>!</p>
            
            <p>Спасибо за регистрацию в RISMENT — вашем надежном партнере в фулфилменте для маркетплейсов Узбекистана.</p>
            
            <p>Ваш аккаунт успешно создан и теперь вы можете пользоваться нашими услугами.</p>
            
            <div class="cta">
                <a href="{{ url('/cabinet') }}" class="btn">Перейти в личный кабинет</a>
            </div>
            
            <div class="features">
                <h3>Что вы можете сделать:</h3>
                <div class="feature-item">Выбрать подходящий пакет услуг</div>
                <div class="feature-item">Создать первую приемку товара</div>
                <div class="feature-item">Отслеживать остатки в режиме реального времени</div>
                <div class="feature-item">Управлять отгрузками FBO</div>
                <div class="feature-item">Получать поддержку через систему тикетов</div>
            </div>
            
            <p><strong>Следующие шаги:</strong></p>
            <ol>
                <li>Войдите в <a href="{{ url('/cabinet') }}">личный кабинет</a></li>
                <li>Выберите подходящий пакет услуг</li>
                <li>Свяжитесь с нами для активации</li>
            </ol>
            
            <p style="margin-top: 30px;">Если у вас есть вопросы, наша команда поддержки всегда готова помочь!</p>
            
            <p style="color: #666; font-size: 14px; margin-top: 20px;">
                📧 Email: info@risment.uz<br>
                📞 Телефон: +998 (71) 123-45-67
            </p>
        </div>
        
        <div class="footer">
            <p>С уважением,<br><strong>Команда RISMENT</strong></p>
            <p style="margin: 10px 0 0 0; font-size: 12px;">Это автоматическое письмо. Пожалуйста, не отвечайте на него.</p>
        </div>
    </div>
</body>
</html>
