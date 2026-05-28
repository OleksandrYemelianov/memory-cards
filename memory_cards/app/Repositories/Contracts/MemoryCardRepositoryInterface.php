<?php

namespace App\Repositories\Contracts;

use App\Models\MemoryCard;
use Illuminate\Database\Eloquent\Collection;

interface MemoryCardRepositoryInterface extends RepositoryInterface
{
    /**
     * @return Collection<int, MemoryCard>
     */
    public function findRandomizedByGroup(int $groupId): Collection;

    public function countByGroup(int $groupId): int;

    public function moveAllBetweenGroups(int $fromGroupId, int $toGroupId): int;
}
