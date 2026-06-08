<?php

namespace App\Http\Controllers;

use App\Http\Requests\LangsRequest;
use App\Repositories\Contracts\LangRepositoryInterface;
use App\Services\AppLangService;
use App\Services\Contracts\TranslatorInterface;
use App\Services\TranslatorFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LangsController extends ResourceController
{
    protected TranslatorInterface $translator;

    public function __construct(
        TranslatorFactory $translatorFactory,
        AppLangService $appLang,
        private LangRepositoryInterface $langs,
    ) {
        parent::__construct($appLang);
        $this->translator = $translatorFactory->make();
    }

    protected function getRepository(): LangRepositoryInterface
    {
        return $this->langs;
    }

    public function create(LangsRequest $request): JsonResponse
    {
        return $this->createEntity($request->validated());
    }

    public function update(LangsRequest $request, int $id): JsonResponse
    {
        return $this->updateEntity($id, $request->validated());
    }

    public function index(): View
    {
        return view('cards.langs', [
            'langs'         => $this->langs->findAll()->toArray(),
            'user_lang'     => Auth::user()->loc,
            'access_langs'  => $this->translator->getAccessLangs(),
            'app_lang_loc'  => $this->app_lang_loc,
        ]);
    }
}
