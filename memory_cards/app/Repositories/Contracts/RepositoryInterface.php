<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * Base CRUD contract shared by all entity repositories.
 */
interface RepositoryInterface
{
    public function findByIdForUser(int $id, int $userId): ?Model;

    public function create(array $attributes): Model;

    public function save(Model $entity, array $attributes): Model;

    public function delete(Model $entity): bool;
}
