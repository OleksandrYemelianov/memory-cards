<?php

namespace App\Repositories\Eloquent;

use App\Models\Langs;
use App\Repositories\Contracts\LangRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentLangRepository implements LangRepositoryInterface
{
    public function __construct(private Langs $model)
    {
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

    public function findByIdForUser(int $id, int $userId): ?Langs
    {
        return $this->model
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    public function save(Langs $lang, array $attributes): Langs
    {
        $lang->fill($attributes)->push();
        return $lang;
    }

    public function delete(Langs $lang): bool
    {
        return (bool) $lang->delete();
    }
}
