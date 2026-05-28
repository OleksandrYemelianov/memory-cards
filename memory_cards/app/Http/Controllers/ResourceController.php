<?php

namespace App\Http\Controllers;

use App\Helpers\UiLangHelper as Lang;
use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Base controller for resources backed by a repository.
 *
 * Provides shared create / update / destroy actions. Concrete controllers
 * supply their specific repository through the getRepository() hook
 * (Template Method pattern). UserLangController extends AppController
 * directly instead, because the User model intentionally has no repository.
 */
abstract class ResourceController extends AppController
{
    abstract protected function getRepository(): RepositoryInterface;

    public function create(): JsonResponse
    {
        return $this->handleExceptions(function () {
            $entity = $this->getRepository()->create($this->request->validated());
            return $this->responseJson(Lang::get('saved'), 200, $entity);
        });
    }

    public function update(int $id): JsonResponse
    {
        $entity = $this->getRepository()->findByIdForUser($id, Auth::id());
        if (!$entity) {
            return $this->responseJson('Record not found', 404);
        }

        return $this->handleExceptions(function () use ($entity) {
            $saved = $this->getRepository()->save($entity, $this->request->validated());
            return $this->responseJson(Lang::get('saved'), 200, $saved);
        });
    }

    public function destroy(int $id): JsonResponse
    {
        $entity = $this->getRepository()->findByIdForUser($id, Auth::id());
        if (!$entity) {
            return $this->responseJson('Record not found', 404);
        }
        $this->getRepository()->delete($entity);

        return $this->responseJson('');
    }
}
