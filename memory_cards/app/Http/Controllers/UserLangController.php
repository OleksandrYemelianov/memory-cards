<?php

namespace App\Http\Controllers;

use App\Helpers\UiLangHelper as Lang;
use App\Http\Requests\UserLangRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserLangController extends AppController
{
    public function __construct(UserLangRequest $request)
    {
        parent::__construct();
        $this->request = $request;
    }

    /**
     * Update the authenticated user's language preference.
     *
     * Works directly on the User model via Auth. We intentionally do not use
     * a repository here: in Laravel, Auth::user() is the idiomatic accessor
     * for the authenticated user, and wrapping it would create a leaky
     * abstraction with two paths to the same entity.
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

            return $this->responseJson(Lang::get('saved'), 200, $user);
        });
    }
}
