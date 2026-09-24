<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogLivewireRequests
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('livewire/*')) {
            Log::info('Livewire request', [
                'url' => $request->fullUrl(),
                'body_keys' => array_keys($request->all()),
            ]);
        }
        return $next($request);
    }
}
