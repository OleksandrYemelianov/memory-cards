<?php

namespace App\Repositories\Eloquent;

use App\Models\MemoryCard;
use App\Repositories\Contracts\MemoryCardRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class EloquentMemoryCardRepository implements MemoryCardRepositoryInterface
{
    public function __construct(private MemoryCard $model)
    {
    }

    public function findByIdForUser(int $id, int $userId): ?MemoryCard
    {
        return $this->model
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    public function create(array $attributes): MemoryCard
    {
        return $this->model->create($attributes);
    }

    public function save(Model $entity, array $attributes): MemoryCard
    {
        /** @var MemoryCard $entity */
        $entity->fill($attributes)->push();
        return $entity;
    }

    public function delete(Model $entity): bool
    {
        return (bool) $entity->delete();
    }

    /**
     * Fetch all cards in a group in randomized order.
     *
     * Why not "ORDER BY RAND()":
     *   ORDER BY RAND() evaluates RAND() for every row and then sorts the whole
     *   result set, forcing a full scan plus an O(n log n) filesort and
     *   preventing index-based ordering. On large tables this is slow.
     *
     * Strategy:
     *   1) Fetch only IDs (indexed lookup).
     *   2) Shuffle the ID array in PHP (Fisher-Yates, O(n)).
     *   3) Fetch full rows by primary key with WHERE IN (...).
     *   4) Reorder the rows to match the shuffled ID order.
     *
     * The UserScope global scope still applies, so tenant isolation holds.
     *
     * @return Collection<int, MemoryCard>
     */
    public function findRandomizedByGroup(int $groupId): Collection
    {
        $ids = $this->model
            ->where('group_id', $groupId)
            ->pluck('id')
            ->all();

        if (empty($ids)) {
            return new Collection();
        }

        shuffle($ids);

        $byId = $this->model
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        $ordered = [];
        foreach ($ids as $id) {
            if ($byId->has($id)) {
                $ordered[] = $byId->get($id);
            }
        }

        return new Collection($ordered);
    }

    public function countByGroup(int $groupId): int
    {
        return $this->model->where('group_id', $groupId)->count();
    }

    public function moveAllBetweenGroups(int $fromGroupId, int $toGroupId): int
    {
        return $this->model
            ->where('group_id', $fromGroupId)
            ->update(['group_id' => $toGroupId]);
    }
}
