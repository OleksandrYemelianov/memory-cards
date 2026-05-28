<?php

namespace App\Repositories\Eloquent;

use App\Models\Langs;
use App\Repositories\Contracts\LangRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class EloquentLangRepository implements LangRepositoryInterface
{
    public function __construct(private Langs $model)
    {
    }

    public function findByIdForUser(int $id, int $userId): ?Langs
    {
        return $this->model
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    public function create(array $attributes): Langs
    {
        return $this->model->create($attributes);
    }

    public function save(Model $entity, array $attributes): Langs
    {
        /** @var Langs $entity */
        $entity->fill($attributes)->push();
        return $entity;
    }

    public function delete(Model $entity): bool
    {
        return (bool) $entity->delete();
    }

    /**
     * @return Collection<int, Langs>
     */
    public function findAll(): Collection
    {
        return $this->model->orderBy('name', 'asc')->get();
    }

    public function findIdByLocale(string $locale): int
    {
        return (int) $this->model
            ->where('loc', $locale)
            ->value('id') ?? 0;
    }
}
