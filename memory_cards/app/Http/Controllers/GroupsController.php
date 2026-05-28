<?php

namespace App\Http\Controllers;

use App\Helpers\GroupsHelper;
use App\Http\Requests\GroupRequest;
use App\Repositories\Contracts\GroupRepositoryInterface;
use App\Repositories\Contracts\LangRepositoryInterface;
use Illuminate\Http\JsonResponse;

class GroupsController extends ResourceController
{
    public function __construct(
        GroupRequest $request,
        private GroupRepositoryInterface $groups,
        private LangRepositoryInterface $langs,
    ) {
        parent::__construct();
        $this->request = $request;
    }

    protected function getRepository(): GroupRepositoryInterface
    {
        return $this->groups;
    }

    public function index(): mixed
    {
        $langs = $this->langs->findAll();
        $groups_info = GroupsHelper::getGroups();
        if (!$langs->count() || empty($groups_info)) {
            return redirect()->route('langs.index');
        }

        $data = [
            'langs' => $langs,
            'groups' => $groups_info['groups'],
            'app_lang_loc' => $this->app_lang_loc,
            'current_group' => $groups_info['curr_group_id'],
        ];

        return view('cards.groups', $data);
    }

    public function getAll(): JsonResponse
    {
        return $this->responseJson('', 200, GroupsHelper::getGroups());
    }
}
