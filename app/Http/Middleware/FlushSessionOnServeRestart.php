<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FlushSessionOnServeRestart
{
    public function handle(Request $request, Closure $next): Response
    {
        // Laravel's local PHP server keeps one process alive between requests.
        // A different PID means `php artisan serve` was restarted.
        if (PHP_SAPI === 'cli-server') {
            $session = $request->session();
            $currentProcess = getmypid();
            $previousProcess = $session->get('_serve_process');

            if ($previousProcess !== null && $previousProcess !== $currentProcess) {
                Auth::logout();
                $session->invalidate();
                $session->regenerateToken();
            }

            $session->put('_serve_process', $currentProcess);
        }

        return $next($request);
    }
}
