<?php

namespace App\Http\Controllers;

use App\Helpers\AppLangHelper;
use App\Helpers\GroupsHelper;
use App\Helpers\UiLangHelper as Lang;
use App\Helpers\UserDataHelper;
use App\Http\Requests\CardRequest;
use App\Models\MemoryCard;
use App\Repositories\Contracts\MemoryCardRepositoryInterface;
use App\Services\Contracts\TranslatorInterface;
use App\Services\TranslatorFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CardController extends AppController
{
    protected TranslatorInterface $translator;

    public function __construct(
        MemoryCard $model,
        CardRequest $request,
        TranslatorFactory $translatorFactory,
        private MemoryCardRepositoryInterface $cards
    ) {
        parent::__construct();
        $this->translator = $translatorFactory->make();
        $this->model = $model;
        $this->request = $request;
    }

    public function index(): mixed
    {
        $groups_info = GroupsHelper::getGroups();
        if (empty($groups_info['groups'])) {
            return redirect()->route('group.index');
        }

        $cards = $this->cards->findRandomizedByGroup($groups_info['curr_group_id']);

        $data = [
            'cards' => $cards,
            'groups' => $groups_info['groups'],
            'current_lang' => AppLangHelper::getLang(),
            'current_group' => $groups_info['curr_group_id'],
        ];

        return view('cards.show', $data);
    }

    public function importCsv(Request $request): JsonResponse
    {
        $file = $request->file('csv_file');
        $fileHandle = fopen($file->getPathname(), 'r');
        $cnt = 0;
        $group_id = (int) $request->get('group_app', 0);
        while (($row = fgetcsv($fileHandle, 1000, ',')) !== false) {
            $this->cards->create([
                'foreign_word' => $row[0],
                'translation' => $row[1],
                'group_id' => $group_id,
            ]);
            $cnt++;
        }
        fclose($fileHandle);

        return $this->responseJson(sprintf(Lang::get('imported_qty_cards'), $cnt));
    }

    public function move(Request $request): JsonResponse
    {
        $group_from = (int) $request->get('from', 0);
        $group_to = (int) $request->get('to', 0);
        if (empty($group_from) || empty($group_to)) {
            return $this->responseJson(Lang::get('select_group'), 500);
        }

        $this->cards->moveAllBetweenGroups($group_from, $group_to);

        return $this->responseJson(Lang::get('saved'));
    }

    public function translate(Request $request): JsonResponse
    {
        if (!$this->translator->checkAccessTranslate()) {
            return $this->responseJson(Lang::get('limit_translate'), 500);
        }
        $foreign = (string) $request->get('foreign', '');
        $translation = (string) $request->get('translation', '');
        if (empty($foreign) && empty($translation)) {
            return $this->responseJson(Lang::get('empty_field'), 500);
        }
        $user_lang = Auth::user()->loc;
        $app_lang = $this->app_lang_loc;
        if (empty($foreign)) {
            $text = $translation;
            $from = $user_lang;
            $to = $app_lang;
        } else {
            $text = $foreign;
            $from = $app_lang;
            $to = $user_lang;
        }

        return $this->responseJson(
            '',
            200,
            $this->translator->translate($text, $from, $to)
        );
    }

    public function importUserData(): JsonResponse
    {
        $res = UserDataHelper::import(Auth::id());
        return $this->responseJson('', $res ? 200 : 500);
    }

    public function exportUserData(): JsonResponse
    {
        $res = UserDataHelper::export(Auth::id());
        return $this->responseJson('', $res ? 200 : 500);
    }
}
