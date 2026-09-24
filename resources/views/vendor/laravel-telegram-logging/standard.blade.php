@php
    $exception = $context['exception'] ?? null;
    $isThrowable = is_object($exception) && $exception instanceof \Throwable;
    $errorMessage = $isThrowable ? $exception->getMessage() : $message;
    $file = $isThrowable ? $exception->getFile() : null;
    $line = $isThrowable ? $exception->getLine() : null;
    $req = request();
    $hasRequest = $req && method_exists($req, 'fullUrl') && !app()->runningInConsole();
@endphp
🚨 <b>خطای جدید در سامانه {{ $appName }}</b>

🏷 <b>سطح خطا:</b> <code>{{ $level_name }}</code>
🌍 <b>محیط:</b> <code>{{ $appEnv }}</code>
⏰ <b>زمان:</b> <code>{{ $datetime->format('Y-m-d H:i:s') }}</code>
@if($hasRequest)

🌐 <b>آدرس:</b> <code>{{ $req->method() }} {{ $req->fullUrl() }}</code>
👤 <b>کاربر:</b> {{ auth()->check() ? 'شناسه ' . auth()->id() . ' (' . (auth()->user()->name ?? auth()->user()->email) . ')' : 'کاربر مهمان (Guest)' }}
📍 <b>IP کاربر:</b> <code>{{ $req->ip() }}</code>
@endif

⚠️ <b>پیام خطا:</b>
<code>{{ \Illuminate\Support\Str::limit($errorMessage ?: 'بدون پیام مشخص', 1000) }}</code>
@if($file)

📁 <b>محل وقوع خطا:</b>
<code>{{ $file }}:{{ $line }}</code>
@endif
@if($isThrowable)

📋 <b>خلاصه ردیابی خطا:</b>
<pre>{{ \Illuminate\Support\Str::limit($exception->getTraceAsString(), 1200) }}</pre>
@endif