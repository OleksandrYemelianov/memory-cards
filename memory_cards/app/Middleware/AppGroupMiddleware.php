<?php

namespace App\Middleware;

use App\Services\CurrentGroupService;
use App\Services\GroupsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AppGroupMiddleware
{
    public function __construct(
        private CurrentGroupService $currentGroup,
        private GroupsService $groups,
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('post')) {
            if ($request->has('group_app')) {
                $this->currentGroup->set((int) $request->post('group_app'));
            }
            if ($request->has('group_qty')) {
                $this->groups->updateQty((int) $request->post('group_qty'));
            }
        }

        return $next($request);
    }
}
