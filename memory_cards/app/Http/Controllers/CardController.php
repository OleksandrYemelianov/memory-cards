<?php

namespace App\Http\Controllers;

use App\Http\Requests\CardRequest;
use App\Repositories\Contracts\MemoryCardRepositoryInterface;
use App\Services\AppLangService;
use App\Services\Contracts\TranslatorInterface;
use App\Services\GroupsService;
use App\Services\TranslatorFactory;
use App\Services\UserDataService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CardController extends ResourceController
{
    protected TranslatorInterface $translator;

    public function __construct(
        CardRequest $request,
        TranslatorFactory $translatorFactory,
        AppLangService $appLang,
        private MemoryCardRepositoryInterface $cards,
        private GroupsService $groups,
        private UserDataService $userData,
    ) {
        parent::__construct($appLang);
        $this->translator = $translatorFactory->make();
        $this->request    = $request;
    }

    protected function getRepository(): MemoryCardRepositoryInterface
    {
        return $this->cards;
    }

    public function index(): View|RedirectResponse
    {
        $groupsInfo = $this->groups->getGroups();
        if (empty($groupsInfo['groups'])) {
            return redirect()->route('group.index');
        }

        $cards = $this->cards->findRandomizedByGroup($groupsInfo['curr_group_id']);

        return view('cards.show', [
            'cards'         => $cards,
            'groups'        => $groupsInfo['groups'],
            'current_lang'  => $this->appLang->getLang(),
            'current_group' => $groupsInfo['curr_group_id'],
        ]);
    }

    public function addView(): View|RedirectResponse
    {
        $groups = $this->groups->getGroups();
        if (empty($groups['groups'])) {
            return redirect()->route('group.index');
        }
        return view('cards.create', $groups);
    }

    /**
     * GET /cards/import — same story as addView(): closure → method.
     */
    public function importView(): View
    {
        return view('cards.import', $this->groups->getGroups());
    }

    public function importCsv(Request $request): JsonResponse
    {
        $file       = $request->file('csv_file');
        $fileHandle = fopen($file->getPathname(), 'r');
        $cnt        = 0;
        $groupId    = (int) $request->get('group_app', 0);
        while (($row = fgetcsv($fileHandle, 1000, ',')) !== false) {
            $this->cards->create([
                'foreign_word' => $row[0],
                'translation'  => $row[1],
                'group_id'     => $groupId,
            ]);
            $cnt++;
        }
        fclose($fileHandle);

        return $this->responseJson(sprintf(__('messages.imported_qty_cards'), $cnt));
    }

    public function move(Request $request): JsonResponse
    {
        $groupFrom = (int) $request->get('from', 0);
        $groupTo   = (int) $request->get('to', 0);
        if (empty($groupFrom) || empty($groupTo)) {
            return $this->responseJson(__('messages.select_group'), 500);
        }

        $this->cards->moveAllBetweenGroups($groupFrom, $groupTo);

        return $this->responseJson(__('messages.saved'));
    }

    public function translate(Request $request): JsonResponse
    {
        if (!$this->translator->checkAccessTranslate()) {
            return $this->responseJson(__('messages.limit_translate'), 500);
        }
        $foreign     = (string) $request->get('foreign', '');
        $translation = (string) $request->get('translation', '');
        if (empty($foreign) && empty($translation)) {
            return $this->responseJson(__('messages.empty_field'), 500);
        }
        $userLang = Auth::user()->loc;
        $appLang  = $this->app_lang_loc;
        if (empty($foreign)) {
            $text = $translation;
            $from = $userLang;
            $to   = $appLang;
        } else {
            $text = $foreign;
            $from = $appLang;
            $to   = $userLang;
        }

        return $this->responseJson(
            '',
            200,
            $this->translator->translate($text, $from, $to)
        );
    }

    public function importUserData(): JsonResponse
    {
        $res = $this->userData->import(Auth::id());
        return $this->responseJson('', $res ? 200 : 500);
    }

    public function exportUserData(): JsonResponse
    {
        $res = $this->userData->export(Auth::id());
        return $this->responseJson('', $res ? 200 : 500);
    }
}
