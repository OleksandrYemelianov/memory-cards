<?php

namespace App\Http\Controllers;

use App\Services\AppLangService;
use App\Traits\AppResponse;
use Illuminate\Http\JsonResponse;

abstract class AppController
{
    use AppResponse;

    protected string $app_lang_loc;
    protected int $app_lang_id;

    public function __construct(protected AppLangService $appLang)
    {
        $this->app_lang_loc = $appLang->getLocale();
        $this->app_lang_id  = $appLang->getId();
    }

    /**
     * Empty endpoint for routes whose work is fully done by middleware
     * (see AppGroupMiddleware handling group_app / group_qty / lang_app).
     */
    public function set(): JsonResponse
    {
        return $this->responseJson('', 200);
    }

    protected function handleExceptions(callable $callback): mixed
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            return $this->responseJson($e->getMessage(), 500);
        }
    }
}
