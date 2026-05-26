<?php

namespace App\Repositories\Contracts;

use App\Models\MemoryCard;
use Illuminate\Database\Eloquent\Collection;

interface MemoryCardRepositoryInterface
{
    public function findByIdForUser(int $id, int $userId): ?MemoryCard;

    /**
     * @return Collection<int, MemoryCard>
     */
    public function findRandomizedByGroup(int $groupId): Collection;

    public function countByGroup(int $groupId): int;

    public function moveAllBetweenGroups(int $fromGroupId, int $toGroupId): int;

    public function create(array $attributes): MemoryCard;

    public function save(MemoryCard $card, array $attributes): MemoryCard;

    public function delete(MemoryCard $card): bool;
}
