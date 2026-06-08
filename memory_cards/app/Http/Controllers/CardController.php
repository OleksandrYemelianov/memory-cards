<?php

namespace App\Http\Controllers;

use App\Http\Requests\CardRequest;
use App\Http\Requests\CsvImportRequest;
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
use Illuminate\Support\Facades\DB;

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

    public function importView(): View
    {
        return view('cards.import', $this->groups->getGroups());
    }

    /**
     * Import cards from an uploaded CSV (one "foreign,translation" pair per line).
     *
     * The whole import runs in a transaction so a failure midway leaves no
     * partial data. The file handle is always released via finally, and empty
     * or incomplete rows are skipped rather than inserted as blank cards.
     *
     * Cards are inserted one by one with create() rather than a bulk insert:
     * the MemoryCard "creating" event sets user_id and a random color, and a
     * bulk insert would bypass those events. For this app's import sizes that
     * trade-off is fine; a much larger import would warrant a batched insert
     * that reproduces those fields explicitly.
     */
    public function importCsv(CsvImportRequest $request): JsonResponse
    {
        $groupId = (int) $request->validated()['group_app'];
        $path    = $request->file('csv_file')->getRealPath();

        $handle = fopen($path, 'r');
        if ($handle === false) {
            return $this->responseJson(__('messages.import_failed'), 500);
        }

        try {
            $count = DB::transaction(function () use ($handle, $groupId) {
                $imported = 0;
                while (($row = fgetcsv($handle)) !== false) {
                    $foreign     = trim($row[0] ?? '');
                    $translation = trim($row[1] ?? '');
                    if ($foreign === '' || $translation === '') {
                        continue;
                    }
                    $this->cards->create([
                        'foreign_word' => $foreign,
                        'translation'  => $translation,
                        'group_id'     => $groupId,
                    ]);
                    $imported++;
                }
                return $imported;
            });
        } catch (\Throwable $e) {
            return $this->responseJson(__('messages.import_failed'), 500);
        } finally {
            fclose($handle);
        }

        return $this->responseJson(sprintf(__('messages.imported_qty_cards'), $count));
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
