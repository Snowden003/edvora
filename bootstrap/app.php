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
        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->is('api/*') || $request->is('broadcasting/*') || $request->expectsJson()) {
                return response()->json(['message' => 'Not found.'], 404);
            }
            return response()->view('errors.404', [], 404);
        });

        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() === 403) {
                if ($request->is('api/*') || $request->is('broadcasting/*') || $request->expectsJson()) {
                    return response()->json(['message' => 'Access denied.'], 403);
                }
                return response()->view('errors.403', [], 403);
            }
        });
    })->create();

$app->booting(function () {
    config([
        'broadcasting.default' => 'pusher',
        'broadcasting.connections.pusher.key' => config('broadcasting.connections.pusher.key') ?: 'e777bf9f25b68e56f962',
        'broadcasting.connections.pusher.secret' => config('broadcasting.connections.pusher.secret') ?: '57e3216f0c584f681a6b',
        'broadcasting.connections.pusher.app_id' => config('broadcasting.connections.pusher.app_id') ?: '2189769',
        'broadcasting.connections.pusher.options.cluster' => config('broadcasting.connections.pusher.options.cluster') ?: 'mt1',
    ]);
});

return $app;
