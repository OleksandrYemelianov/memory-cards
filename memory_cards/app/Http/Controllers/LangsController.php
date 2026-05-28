<?php

namespace App\Http\Controllers;

use App\Http\Requests\LangsRequest;
use App\Repositories\Contracts\LangRepositoryInterface;
use App\Services\Contracts\TranslatorInterface;
use App\Services\TranslatorFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LangsController extends ResourceController
{
    protected TranslatorInterface $translator;

    public function __construct(
        LangsRequest $request,
        TranslatorFactory $translatorFactory,
        private LangRepositoryInterface $langs,
    ) {
        parent::__construct();
        $this->translator = $translatorFactory->make();
        $this->request = $request;
    }

    protected function getRepository(): LangRepositoryInterface
    {
        return $this->langs;
    }

    public function index(): View
    {
        $data = [
            'langs' => $this->langs->findAll()->toArray(),
            'user_lang' => Auth::user()->loc,
            'access_langs' => $this->translator->getAccessLangs(),
            'app_lang_loc' => $this->app_lang_loc,
        ];

        return view('cards.langs', $data);
    }
}
