<?php

namespace App\Repositories\Eloquent;

use App\Models\MemoryCard;
use App\Repositories\Contracts\MemoryCardRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

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

    /**
     * Fetch all cards in a group in randomized order.
     *
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

    public function create(array $attributes): MemoryCard
    {
        return $this->model->create($attributes);
    }

    public function save(MemoryCard $card, array $attributes): MemoryCard
    {
        $card->fill($attributes)->push();
        return $card;
    }

    public function delete(MemoryCard $card): bool
    {
        return (bool) $card->delete();
    }
}
