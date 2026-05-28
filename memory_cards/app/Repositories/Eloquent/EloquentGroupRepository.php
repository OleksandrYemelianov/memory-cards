<?php

namespace App\Repositories\Eloquent;

use App\Models\Groups;
use App\Repositories\Contracts\GroupRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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

    public function create(array $attributes): Groups
    {
        return $this->model->create($attributes);
    }

    public function save(Model $entity, array $attributes): Groups
    {
        /** @var Groups $entity */
        $entity->fill($attributes)->push();
        return $entity;
    }

    public function delete(Model $entity): bool
    {
        return (bool) $entity->delete();
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
}
