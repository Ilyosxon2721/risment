<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Telegram Bot Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Telegram Bot notifications.
    | The bot_token is used for sending notifications to company chat_ids.
    | The bot_username is displayed in the connection instructions.
    |
    */

    'bot_token' => env('TELEGRAM_BOT_TOKEN'),
    'bot_username' => env('TELEGRAM_BOT_USERNAME', 'risment_bot'),

];
