<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Repositories\Contracts\GroupRepositoryInterface;
use App\Repositories\Contracts\LangRepositoryInterface;
use App\Services\AppLangService;
use App\Services\GroupsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class GroupsController extends ResourceController
{
    public function __construct(
        AppLangService $appLang,
        private GroupRepositoryInterface $groups,
        private LangRepositoryInterface $langs,
        private GroupsService $groupsService,
    ) {
        parent::__construct($appLang);
    }

    protected function getRepository(): GroupRepositoryInterface
    {
        return $this->groups;
    }

    public function create(GroupRequest $request): JsonResponse
    {
        return $this->createEntity($request->validated());
    }

    public function update(GroupRequest $request, int $id): JsonResponse
    {
        return $this->updateEntity($id, $request->validated());
    }

    public function index(): View|RedirectResponse
    {
        $langs      = $this->langs->findAll();
        $groupsInfo = $this->groupsService->getGroups();
        if (!$langs->count() || empty($groupsInfo)) {
            return redirect()->route('langs.index');
        }

        return view('cards.groups', [
            'langs'         => $langs,
            'groups'        => $groupsInfo['groups'],
            'app_lang_loc'  => $this->app_lang_loc,
            'current_group' => $groupsInfo['curr_group_id'],
        ]);
    }

    public function getAll(): JsonResponse
    {
        return $this->responseJson('', 200, $this->groupsService->getGroups());
    }
}
