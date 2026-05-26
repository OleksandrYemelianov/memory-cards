<?php

namespace App\Repositories\Contracts;

use App\Models\Groups;
use Illuminate\Database\Eloquent\Collection;

interface GroupRepositoryInterface
{
    public function findByIdForUser(int $id, int $userId): ?Groups;

    /**
     * @return Collection<int, Groups>
     */
    public function findAllByLanguage(int $langId): Collection;

    public function updateQuantity(int $id, int $quantity): bool;

    public function create(array $attributes): Groups;

    public function save(Groups $group, array $attributes): Groups;

    public function delete(Groups $group): bool;
}
