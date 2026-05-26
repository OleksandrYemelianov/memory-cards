<?php

namespace App\Repositories\Contracts;

use App\Models\Langs;
use Illuminate\Database\Eloquent\Collection;

interface LangRepositoryInterface
{
    /**
     * @return Collection<int, Langs>
     */
    public function findAll(): Collection;

    public function findIdByLocale(string $locale): int;

    public function findByIdForUser(int $id, int $userId): ?Langs;

    public function save(Langs $lang, array $attributes): Langs;

    public function delete(Langs $lang): bool;
}
