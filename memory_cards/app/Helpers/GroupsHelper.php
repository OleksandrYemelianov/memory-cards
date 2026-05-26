<?php

namespace App\Helpers;

use App\Models\Groups;
use App\Models\User;
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
        $groups = Groups::getAll($lang_id)->toArray();
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
     * Update the quantity for a specific group.
     */
    public static function updateQty(int $group_id): void
    {
        $cards = app(MemoryCardRepositoryInterface::class);
        $qty = $cards->countByGroup($group_id);

        Groups::where('id', $group_id)->update(['qty' => $qty]);
    }
}
