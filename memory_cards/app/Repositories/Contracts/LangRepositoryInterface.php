<?php

namespace App\Repositories\Contracts;

use App\Models\Langs;
use Illuminate\Database\Eloquent\Collection;

interface LangRepositoryInterface extends RepositoryInterface
{
    /**
     * @return Collection<int, Langs>
     */
    public function findAll(): Collection;

    public function findIdByLocale(string $locale): int;
}
