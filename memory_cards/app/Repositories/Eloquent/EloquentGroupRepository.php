<?php

namespace App\Repositories\Eloquent;

use App\Models\Groups;
use App\Repositories\Contracts\GroupRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentGroupRepository implements GroupRepositoryInterface
{
    public function __construct(private Groups $model)
    {
    }

    public function findByIdForUser(int $id, int $userId): ?Groups
    {
        return $this->model
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * @return Collection<int, Groups>
     */
    public function findAllByLanguage(int $langId): Collection
    {
        return $this->model
            ->where('lang_id', $langId)
            ->orderBy('name', 'asc')
            ->get();
    }

    public function updateQuantity(int $id, int $quantity): bool
    {
        return (bool) $this->model
            ->where('id', $id)
            ->update(['qty' => $quantity]);
    }

    public function create(array $attributes): Groups
    {
        return $this->model->create($attributes);
    }

    public function save(Groups $group, array $attributes): Groups
    {
        $group->fill($attributes)->push();
        return $group;
    }

    public function delete(Groups $group): bool
    {
        return (bool) $group->delete();
    }
}
