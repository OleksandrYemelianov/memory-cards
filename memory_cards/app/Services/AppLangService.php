<?php

namespace App\Services;

use App\Models\Langs;
use App\Models\User;
use App\Repositories\Contracts\LangRepositoryInterface;
use Illuminate\Http\Request;

/**
 * Manages the "language being learnt" preference on the authenticated user.
 *
 */
class AppLangService
{
    public function __construct(
        private LangRepositoryInterface $langs,
        private CurrentGroupService $currentGroup,
    ) {
    }

    public function setFromRequest(Request $request): void
    {
        if ($request->isMethod('post') && $request->has('lang_app')) {
            $this->set((string) $request->post('lang_app'));
            $this->currentGroup->remove();
        }
    }

    public function getLocale(): string
    {
        return auth()->user()?->learn_lang ?? '';
    }

    public function set(string $locale): void
    {
        /** @var User|null $user */
        $user = auth()->user();
        $user?->update(['learn_lang' => $locale]);
    }

    public function getId(): int
    {
        $loc = $this->getLocale();
        if (empty($loc)) {
            return 0;
        }

        return $this->langs->findIdByLocale($loc);
    }

    /**
     * Eloquent ::find() is intentional here — wrapping built-in finders in the
     * repository would add boilerplate without architectural benefit (same
     * note as on the old helper).
     */
    public function getLang(): ?Langs
    {
        $id = $this->getId();
        if ($id === 0) {
            return null;
        }

        return Langs::find($id);
    }
}
