<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function (Response $response) {
            if ($response->getStatusCode() === 419) {
                return back()->with(['error' => 'The page expired, please try again.']);
            } elseif ($response->getStatusCode() === 405) {
                \Illuminate\Support\Facades\Log::warning('message: Method Not Allowed (405)', [
                    'url'    => request()->fullUrl(),
                    'method' => request()->method(),
                    'body'   => request()->all(),
                ]);
            }
            return $response;
        });
    })->create();
