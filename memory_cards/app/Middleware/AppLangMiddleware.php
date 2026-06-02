<?php

namespace App\Middleware;

use App\Services\AppLangService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AppLangMiddleware
{
    public function __construct(private AppLangService $lang)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->lang->setFromRequest($request);

        return $next($request);
    }
}
