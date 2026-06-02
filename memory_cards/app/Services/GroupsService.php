<?php

namespace App\Services;

use App\Repositories\Contracts\GroupRepositoryInterface;
use App\Repositories\Contracts\MemoryCardRepositoryInterface;

/**
 * Higher-level operations on groups that mix repository data with the
 * authenticated user's current-group pointer.
 *
 * Repositories handle persistence; this service composes them with the
 * "which group is the user currently on" concern (CurrentGroupService)
 * and the user's learn-language (AppLangService).
 */
class GroupsService
{
    public function __construct(
        private GroupRepositoryInterface $groups,
        private MemoryCardRepositoryInterface $cards,
        private CurrentGroupService $currentGroup,
        private AppLangService $lang,
    ) {
    }

    /**
     * @return array{curr_group_id: int, groups: array<int, array<string, mixed>>}|array{}
     */
    public function getGroups(int $langId = 0): array
    {
        if (empty($langId)) {
            $langId = $this->lang->getId();
        }
        if (empty($langId)) {
            return [];
        }

        $groups       = $this->groups->findAllByLanguage($langId)->toArray();
        $currGroupId  = $this->currentGroup->get();

        if (!empty($groups)) {
            $groupKeys  = array_column($groups, 'id');
            $groupIndex = array_search($currGroupId, $groupKeys, true);
            if ($groupIndex === false) {
                $currGroupId = $groupKeys[0];
                $this->currentGroup->set($currGroupId);
            }
        }

        return [
            'curr_group_id' => $currGroupId,
            'groups'        => $groups,
        ];
    }

    public function updateQty(int $groupId): void
    {
        $qty = $this->cards->countByGroup($groupId);
        $this->groups->updateQuantity($groupId, $qty);
    }
}
