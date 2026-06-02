<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLangRequest;
use App\Models\User;
use App\Services\AppLangService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserLangController extends AppController
{
    public function __construct(UserLangRequest $request, AppLangService $appLang)
    {
        parent::__construct($appLang);
        $this->request = $request;
    }

    /**
     * Update the authenticated user's language preference.
     */
    public function updateUserLang(): JsonResponse
    {
        return $this->handleExceptions(function () {
            /** @var User|null $user */
            $user = Auth::user();
            if (!$user) {
                return $this->responseJson('Unauthorized', 401);
            }

            $user->fill($this->request->validated())->save();

            return $this->responseJson(__('messages.saved'), 200, $user);
        });
    }
}
