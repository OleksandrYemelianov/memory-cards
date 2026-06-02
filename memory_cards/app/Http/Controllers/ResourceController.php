<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Base controller for resources backed by a repository.
 */
abstract class ResourceController extends AppController
{
    abstract protected function getRepository(): RepositoryInterface;

    public function create(): JsonResponse
    {
        return $this->handleExceptions(function () {
            $entity = $this->getRepository()->create($this->request->validated());
            return $this->responseJson(__('messages.saved'), 200, $entity);
        });
    }

    public function update(int $id): JsonResponse
    {
        $entity = $this->getRepository()->findByIdForUser($id, Auth::id());
        if (!$entity) {
            return $this->responseJson(__('messages.Record not found'), 404);
        }

        return $this->handleExceptions(function () use ($entity) {
            $saved = $this->getRepository()->save($entity, $this->request->validated());
            return $this->responseJson(__('messages.saved'), 200, $saved);
        });
    }

    public function destroy(int $id): JsonResponse
    {
        $entity = $this->getRepository()->findByIdForUser($id, Auth::id());
        if (!$entity) {
            return $this->responseJson(__('messages.Record not found'), 404);
        }
        $this->getRepository()->delete($entity);

        return $this->responseJson('');
    }
}
