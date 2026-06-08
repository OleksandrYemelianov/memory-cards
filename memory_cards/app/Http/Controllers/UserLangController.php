<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLangRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserLangController extends AppController
{
    public function updateUserLang(UserLangRequest $request): JsonResponse
    {
        return $this->handleExceptions(function () use ($request) {
            /** @var User|null $user */
            $user = Auth::user();
            if (!$user) {
                return $this->responseJson('Unauthorized', 401);
            }

            $user->fill($request->validated())->save();

            return $this->responseJson(__('messages.saved'), 200, $user);
        });
    }
}
