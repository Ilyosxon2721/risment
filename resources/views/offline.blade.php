<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#7c3aed">
    <title>{{ __('Offline') }} - RISMENT</title>
    <style>
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #1e1145 0%, #2d1a5e 30%, #3b2070 50%, #2d1a5e 70%, #1e1145 100%);
            color: #ffffff;
            padding: 20px;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(ellipse at 30% 20%, rgba(124, 58, 237, 0.15) 0%, transparent 50%),
                        radial-gradient(ellipse at 70% 80%, rgba(124, 58, 237, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        .container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
            text-align: center;
        }

        .card {
            background: rgba(255, 255, 255, 0.07);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 24px;
            padding: 48px 36px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3),
                        inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        .logo {
            width: 72px;
            height: 72px;
            margin: 0 auto 28px;
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -1px;
            box-shadow: 0 4px 20px rgba(124, 58, 237, 0.4);
        }

        .offline-icon {
            margin-bottom: 24px;
        }

        .offline-icon svg {
            width: 64px;
            height: 64px;
            opacity: 0.6;
        }

        .title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 12px;
            line-height: 1.3;
        }

        .subtitle {
            font-size: 15px;
            color: rgba(255, 255, 255, 0.6);
            line-height: 1.5;
            margin-bottom: 32px;
        }

        .retry-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 36px;
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            color: #ffffff;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 16px rgba(124, 58, 237, 0.35);
            letter-spacing: 0.01em;
            text-decoration: none;
        }

        .retry-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 24px rgba(124, 58, 237, 0.5);
        }

        .retry-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(124, 58, 237, 0.3);
        }

        .retry-btn svg {
            width: 18px;
            height: 18px;
        }

        .brand-name {
            margin-top: 32px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.3);
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        @media (max-width: 480px) {
            .card {
                padding: 36px 24px;
                border-radius: 20px;
            }

            .title {
                font-size: 20px;
            }

            .subtitle {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="logo">R</div>

            <div class="offline-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.5)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="1" y1="1" x2="23" y2="23"></line>
                    <path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55"></path>
                    <path d="M5 12.55a10.94 10.94 0 0 1 5.17-2.39"></path>
                    <path d="M10.71 5.05A16 16 0 0 1 22.56 9"></path>
                    <path d="M1.42 9a15.91 15.91 0 0 1 4.7-2.88"></path>
                    <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
                    <line x1="12" y1="20" x2="12.01" y2="20"></line>
                </svg>
            </div>

            <h1 class="title">{{ __('No internet connection') }}</h1>
            <p class="subtitle">{{ __('Check your connection and try again. Previously visited pages may be available from cache.') }}</p>

            <button class="retry-btn" onclick="window.location.reload()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="23 4 23 10 17 10"></polyline>
                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                </svg>
                <span>{{ __('Retry') }}</span>
            </button>

            <div class="brand-name">RISMENT</div>
        </div>
    </div>
</body>
</html>
