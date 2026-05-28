<?php

namespace App\Http\Controllers;

use App\Helpers\AppLangHelper;
use App\Traits\AppResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

abstract class AppController
{
    use AppResponse;

    protected string $app_lang_loc;
    protected int $app_lang_id;
    protected FormRequest $request;

    public function __construct()
    {
        $this->app_lang_loc = AppLangHelper::getLocale();
        $this->app_lang_id  = AppLangHelper::getId();
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
