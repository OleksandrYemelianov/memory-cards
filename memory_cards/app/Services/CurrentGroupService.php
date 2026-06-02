<?php

namespace App\Services;

use App\Models\User;

/**
 * Manages the authenticated user's "current group" pointer.
 */
class CurrentGroupService
{
    public function set(int $groupId): void
    {
        /** @var User|null $user */
        $user = auth()->user();
        $user?->update(['current_group' => $groupId]);
    }

    public function get(): int
    {
        return auth()->user()?->current_group ?? 0;
    }

    public function remove(): void
    {
        /** @var User|null $user */
        $user = auth()->user();
        $user?->update(['current_group' => null]);
    }
}
