<?php

namespace App\Logging;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;
use Monolog\Logger;
use Illuminate\Support\Str;

class TelegramHandler extends AbstractProcessingHandler
{
    protected array $config;
    protected ?string $botToken;
    protected ?string $chatId;
    protected ?string $messageThreadId;
    protected string $appName;
    protected string $appEnv;

    public function __construct(array $config = [])
    {
        $level = isset($config['level']) ? Logger::toMonologLevel($config['level']) : Logger::DEBUG;
        parent::__construct($level, true);

        $this->config = $config;
        $this->botToken = $config['token'] ?? config('telegram-logger.token') ?? env('TELEGRAM_LOGGER_BOT_TOKEN', env('TELEGRAM_BOT_TOKEN'));
        $this->chatId = $config['chat_id'] ?? config('telegram-logger.chat_id') ?? env('TELEGRAM_LOGGER_CHAT_ID', env('TELEGRAM_CHAT_ID'));
        $this->messageThreadId = $config['message_thread_id'] ?? config('telegram-logger.message_thread_id') ?? env('TELEGRAM_LOGGER_MESSAGE_THREAD_ID');
        $this->appName = config('app.name', 'Laravel');
        $this->appEnv = config('app.env', 'production');
    }

    protected function write(LogRecord $record): void
    {
        if (empty($this->botToken) || empty($this->chatId)) {
            return;
        }

        $htmlMessage = $this->buildHtmlMessage($record);
        $this->sendToTelegram($htmlMessage, true, $record);
    }

    protected function buildHtmlMessage(LogRecord $record): string
    {
        $context = $record->context;
        $exception = $context['exception'] ?? null;
        $isThrowable = is_object($exception) && $exception instanceof \Throwable;

        $levelName = htmlspecialchars($record->level->getName(), ENT_QUOTES, 'UTF-8');
        $appName = htmlspecialchars($this->appName, ENT_QUOTES, 'UTF-8');
        $appEnv = htmlspecialchars($this->appEnv, ENT_QUOTES, 'UTF-8');
        $datetime = $record->datetime->format('Y-m-d H:i:s');

        // Check if error is 404 or specific code
        $statusCode = $context['status_code'] ?? null;
        if (!$statusCode && $isThrowable && method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();
        }

        $titleEmoji = '🚨';
        $titlePrefix = 'خطای جدید در سامانه';
        if ($statusCode === 404) {
            $titleEmoji = '🔍';
            $titlePrefix = 'خطای ۴۰۴ (صفحه یافت نشد)';
        } elseif ($statusCode === 403) {
            $titleEmoji = '⛔';
            $titlePrefix = 'خطای ۴۰۳ (دسترسی غیرمجاز)';
        }

        $lines = [];
        $lines[] = "{$titleEmoji} <b>{$titlePrefix} {$appName}</b>";
        $lines[] = "🏷 <b>سطح:</b> <code>{$levelName}</code> | 🌍 <b>محیط:</b> <code>{$appEnv}</code>";
        $lines[] = "⏰ <b>زمان:</b> <code>{$datetime}</code>";

        // Web Request Details if available
        $req = request();
        if ($req && method_exists($req, 'fullUrl') && !app()->runningInConsole()) {
            $method = htmlspecialchars($req->method(), ENT_QUOTES, 'UTF-8');
            $url = htmlspecialchars($req->fullUrl(), ENT_QUOTES, 'UTF-8');
            $ip = htmlspecialchars($req->ip() ?? 'نامشخص', ENT_QUOTES, 'UTF-8');

            $userInfo = 'کاربر مهمان (Guest)';
            if (auth()->check()) {
                $user = auth()->user();
                $userInfo = 'شناسه ' . $user->id . ' (' . htmlspecialchars($user->name ?? $user->email ?? 'کاربر', ENT_QUOTES, 'UTF-8') . ')';
            }

            $lines[] = "";
            $lines[] = "🌐 <b>آدرس:</b> <code>{$method} {$url}</code>";
            $lines[] = "👤 <b>کاربر:</b> {$userInfo}";
            $lines[] = "📍 <b>IP کاربر:</b> <code>{$ip}</code>";
        } elseif (app()->runningInConsole()) {
            $cmd = implode(' ', $_SERVER['argv'] ?? []);
            $lines[] = "";
            $lines[] = "💻 <b>دستور ترمینال (CLI):</b> <code>" . htmlspecialchars($cmd ?: 'artisan command', ENT_QUOTES, 'UTF-8') . "</code>";
        }

        // Error message
        $rawMessage = $isThrowable ? $exception->getMessage() : $record->message;
        if (empty($rawMessage) && $isThrowable) {
            $rawMessage = get_class($exception);
        }
        $escapedMessage = htmlspecialchars(Str::limit($rawMessage, 1000), ENT_QUOTES, 'UTF-8');

        $lines[] = "";
        $lines[] = "⚠️ <b>پیام خطا:</b>";
        $lines[] = "<code>{$escapedMessage}</code>";

        // File and Line
        if ($isThrowable) {
            $file = htmlspecialchars($exception->getFile() . ':' . $exception->getLine(), ENT_QUOTES, 'UTF-8');
            $lines[] = "";
            $lines[] = "📁 <b>محل وقوع خطا:</b>";
            $lines[] = "<code>{$file}</code>";

            // Trace snippet (max 800 chars)
            $trace = Str::limit($exception->getTraceAsString(), 800);
            $lines[] = "";
            $lines[] = "📋 <b>خلاصه ردیابی خطا:</b>";
            $lines[] = "<pre>" . htmlspecialchars($trace, ENT_QUOTES, 'UTF-8') . "</pre>";
        }

        return implode("\n", $lines);
    }

    protected function sendToTelegram(string $text, bool $isHtml = true, ?LogRecord $record = null): void
    {
        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";

        $postData = [
            'chat_id' => $this->chatId,
            'text' => $text,
        ];

        if ($isHtml) {
            $postData['parse_mode'] = 'html';
        }

        if (!empty($this->messageThreadId)) {
            $postData['message_thread_id'] = $this->messageThreadId;
        }

        $proxy = $this->config['proxy'] ?? config('telegram-logger.proxy') ?? env('TELEGRAM_LOGGER_PROXY');

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        if (!empty($proxy)) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // If SSL issue, retry once with SSL_VERIFYPEER = false
        if ($response === false && (str_contains($curlError, 'SSL') || str_contains($curlError, 'certificate'))) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_TIMEOUT => 8,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
        }

        // If Telegram rejected due to HTML parsing error, fallback to plain text
        if ($httpCode !== 200 && $isHtml && $response) {
            $resData = json_decode($response, true);
            if (isset($resData['description']) && str_contains(strtolower($resData['description']), 'can\'t parse entities')) {
                $plainText = strip_tags($text);
                $this->sendToTelegram($plainText, false, $record);
            }
        }
    }
}
