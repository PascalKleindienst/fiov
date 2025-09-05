<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Facades\LicenseService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class RequiresLicenseMiddleware
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_if(LicenseService::isCommunity(), 403);

        return $next($request);
    }
}
