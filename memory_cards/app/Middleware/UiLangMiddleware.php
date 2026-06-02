<?php

namespace App\Middleware;

use App\Services\UiLangService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class UiLangMiddleware
{
    public function __construct(private UiLangService $lang)
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
        App::setLocale($this->lang->getLocale());

        return $next($request);
    }
}
