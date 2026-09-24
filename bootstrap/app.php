<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Auto-clean stale bootstrap cache if present
if (file_exists(__DIR__.'/cache/config.php')) {
    @unlink(__DIR__.'/cache/config.php');
}
if (file_exists(__DIR__.'/cache/routes-v7.php')) {
    @unlink(__DIR__.'/cache/routes-v7.php');
}

// Fallback autoloader for Inertia on shared hosting environments without Composer
if (!class_exists(\Inertia\Inertia::class)) {
    $inertiaSrc = dirname(__DIR__) . '/vendor/inertiajs/inertia-laravel/src';
    if (is_dir($inertiaSrc)) {
        spl_autoload_register(function ($class) use ($inertiaSrc) {
            $prefix = 'Inertia\\';
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                return;
            }
            $relativeClass = substr($class, $len);
            $file = $inertiaSrc . '/' . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        });
        $helpers = dirname(__DIR__) . '/vendor/inertiajs/inertia-laravel/helpers.php';
        if (file_exists($helpers)) {
            require_once $helpers;
        }
    }
}

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withBroadcasting(
        __DIR__.'/../routes/channels.php',
        ['middleware' => ['web', 'auth']],
    )
    ->withMiddleware(function (Middleware $middleware): void {
        if (class_exists(\Inertia\Middleware::class)) {
            $middleware->web(append: [
                \App\Http\Middleware\HandleInertiaRequests::class,
            ]);
        }
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'teacher.onboarded' => \App\Http\Middleware\EnsureTeacherOnboarded::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Send 404 Not Found errors to Telegram
        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            try {
                \Illuminate\Support\Facades\Log::channel('telegram')->warning('صفحه مورد نظر یافت نشد (404 Not Found)', [
                    'status_code' => 404,
                    'exception' => $e,
                ]);
            } catch (\Throwable $t) {}

            if ($request->is('api/*') || $request->is('broadcasting/*') || $request->expectsJson()) {
                return response()->json(['message' => 'Not found.'], 404);
            }
            return response()->view('errors.404', [], 404);
        });

        // Send 403 Forbidden errors to Telegram
        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() === 403) {
                try {
                    \Illuminate\Support\Facades\Log::channel('telegram')->warning('دسترسی غیرمجاز (403 Forbidden)', [
                        'status_code' => 403,
                        'exception' => $e,
                    ]);
                } catch (\Throwable $t) {}

                if ($request->is('api/*') || $request->is('broadcasting/*') || $request->expectsJson()) {
                    return response()->json(['message' => 'Access denied.'], 403);
                }
                return response()->view('errors.403', [], 403);
            }
        });

        // Stop ignoring HTTP exceptions so all exceptions trigger reporting
        $exceptions->stopIgnoring(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $exceptions->stopIgnoring(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        // Catch and report any unhandled exception (including database/server errors) to Telegram
        $exceptions->report(function (\Throwable $e) {
            try {
                \Illuminate\Support\Facades\Log::channel('telegram')->error($e->getMessage() ?: get_class($e), [
                    'exception' => $e,
                ]);
            } catch (\Throwable $t) {}
        });
    })->create();

$app->booting(function () use ($app) {
    if (class_exists(\Inertia\ServiceProvider::class) && !$app->providerIsLoaded(\Inertia\ServiceProvider::class)) {
        $app->register(\Inertia\ServiceProvider::class);
    }

    config([
        'broadcasting.default' => 'pusher',
        'broadcasting.connections.pusher.key' => config('broadcasting.connections.pusher.key') ?: 'e777bf9f25b68e56f962',
        'broadcasting.connections.pusher.secret' => config('broadcasting.connections.pusher.secret') ?: '57e3216f0c584f681a6b',
        'broadcasting.connections.pusher.app_id' => config('broadcasting.connections.pusher.app_id') ?: '2189769',
        'broadcasting.connections.pusher.options.cluster' => config('broadcasting.connections.pusher.options.cluster') ?: 'mt1',
    ]);
});

return $app;
