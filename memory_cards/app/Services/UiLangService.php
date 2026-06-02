<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;

/**
 * Manages the "UI language" preference on the authenticated user.
 */
class UiLangService
{
    public function getLocale(): string
    {
        return auth()->user()?->ui_lang ?? config('app.locale');
    }

    public function setFromRequest(Request $request): void
    {
        if ($request->has('lang_ui')) {
            $this->set((string) $request->get('lang_ui'));
        }
    }

    public function set(string $locale): void
    {
        /** @var User|null $user */
        $user = auth()->user();
        $user?->update(['ui_lang' => $locale]);
    }
}
