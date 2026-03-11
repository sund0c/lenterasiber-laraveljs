<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;

/**
 * Logs every admin panel page access to audit log.
 * Only logs mutating requests (POST, PUT, PATCH, DELETE).
 */
class AuditMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only log write operations
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            AuditLog::record(
                'admin.' . strtolower($request->method()) . '.' . $request->route()?->getName(),
                null,
                null,
                null,
                ['url' => $request->path(), 'status' => $response->getStatusCode()]
            );
        }

        return $response;
    }
}
