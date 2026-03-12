<?php

use App\Http\Middleware\AuthFullMiddleware;
use App\Http\Middleware\AuthStep1Middleware;
use App\Http\Middleware\AuditMiddleware;
use App\Http\Middleware\SecurityHeadersMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Apply security headers globally
        $middleware->append(SecurityHeadersMiddleware::class);
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\ForcePasswordChangeMiddleware::class,
        ]);

        // Named middleware aliases
        $middleware->alias([
            'auth.step1' => AuthStep1Middleware::class,
            'auth.full'  => AuthFullMiddleware::class,
            'auth.audit' => AuditMiddleware::class,
            'role'                  => \App\Http\Middleware\RoleMiddleware::class,
            'force.password.change' => \App\Http\Middleware\ForcePasswordChangeMiddleware::class,
        ]);

        // Trusted proxies (adjust for your Nginx/load balancer)
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Don't leak stack traces in production
    })
    ->create();
