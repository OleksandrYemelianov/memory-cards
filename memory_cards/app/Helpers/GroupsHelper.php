<?php

namespace App\Helpers;

use App\Models\User;
use App\Repositories\Contracts\GroupRepositoryInterface;
use App\Repositories\Contracts\MemoryCardRepositoryInterface;

class GroupsHelper
{
    /**
     * Set the current group for the authenticated user.
     */
    public static function setCurrentGroup(int $group_id): void
    {
        /** @var User $user */
        $user = auth()->user();
        $user?->update(['current_group' => $group_id]);
    }

    /**
     * Get the current group of the authenticated user.
     */
    public static function getCurrentGroup(): int
    {
        return auth()->user()?->current_group ?? 0;
    }

    /**
     * Remove the current group for the authenticated user.
     */
    public static function removeCurrentGroup(): void
    {
        /** @var User $user */
        $user = auth()->user();
        $user?->update(['current_group' => null]);
    }

    /**
     * Retrieve all groups for a specific language.
     */
    public static function getGroups(int $lang_id = 0): array
    {
        if (empty($lang_id)) {
            $lang_id = AppLangHelper::getId();
        }
        if (empty($lang_id)) {
            return [];
        }

        $groupRepo = app(GroupRepositoryInterface::class);
        $groups = $groupRepo->findAllByLanguage($lang_id)->toArray();

        $curr_group_id = self::getCurrentGroup();
        if (!empty($groups)) {
            $group_keys = array_column($groups, 'id');
            $group_index = array_search($curr_group_id, $group_keys);
            if ($group_index === false) {
                $curr_group_id = $group_keys[0];
                self::setCurrentGroup($curr_group_id);
            }
        }

        return [
            'curr_group_id' => $curr_group_id,
            'groups' => $groups,
        ];
    }

    /**
     * Update the cached card quantity for a specific group.
     *
     * Both the card count and the group update go through repositories now —
     * no direct Eloquent model calls in this helper.
     */
    public static function updateQty(int $group_id): void
    {
        $cards = app(MemoryCardRepositoryInterface::class);
        $groups = app(GroupRepositoryInterface::class);

        $qty = $cards->countByGroup($group_id);
        $groups->updateQuantity($group_id, $qty);
    }
}
