<?php

namespace App\Helpers;

use App\Models\Langs;
use App\Models\User;
use App\Repositories\Contracts\LangRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Suppoes\Auth;

rt\Facad

class AppLangHelper
{
    /**
     * Set the application locale based on the request.
     */
    public static function setLocale(Request $request): void
    {
        if ($request->isMethod('post') && $request->has('lang_app')) {
            $locale = $request->post('lang_app');
            self::set($locale);
            GroupsHelper::removeCurrentGroup();
        }
    }

    /**
     * Get the current user's locale.
     */
    public static function getLocale(): string
    {
        return auth()->user()?->learn_lang ?? '';
    }

    /**
     * Set the current user's locale.
     */
    public static function set(string $locale): void
    {
        /** @var User $user */
        $user = auth()->user();
        $user->update(['learn_lang' => $locale]);
    }

    /**
     * Get the ID of the current locale.
     */
    public static function getId(): int
    {
        $loc = self::getLocale();
        if (empty($loc)) {
            return 0;
        }

        return app(LangRepositoryInterface::class)->findIdByLocale($loc);
    }

    /**
     * Retrieve the Langs model for the current locale.
     *
     * Note: Langs::find() is Laravel's built-in Eloquent finder, not a custom
     * static query method. The refactor only removed our own ad-hoc statics
     * (Langs::getAll(), Langs::getIdByLang()); wrapping every built-in finder
     * would add boilerplate without architectural benefit.
     */
    public static function getLang(): Langs
    {
        return Langs::find(self::getId());
    }
}
