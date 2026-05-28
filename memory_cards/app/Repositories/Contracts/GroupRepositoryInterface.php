<?php

namespace App\Repositories\Contracts;

use App\Models\Groups;
use Illuminate\Database\Eloquent\Collection;

interface GroupRepositoryInterface extends RepositoryInterface
{
    /**
     * @return Collection<int, Groups>
     */
    public function findAllByLanguage(int $langId): Collection;

    public function updateQuantity(int $id, int $quantity): bool;
}
