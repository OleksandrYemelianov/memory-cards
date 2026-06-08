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

    public function destroy(int $id): JsonResponse
    {
        $entity = $this->getRepository()->findByIdForUser($id, Auth::id());
        if (!$entity) {
            return $this->responseJson(__('messages.Record not found'), 404);
        }
        $this->getRepository()->delete($entity);

        return $this->responseJson('');
    }

    protected function createEntity(array $data): JsonResponse
    {
        return $this->handleExceptions(function () use ($data) {
            $entity = $this->getRepository()->create($data);
            return $this->responseJson(__('messages.saved'), 200, $entity);
        });
    }

    protected function updateEntity(int $id, array $data): JsonResponse
    {
        $entity = $this->getRepository()->findByIdForUser($id, Auth::id());
        if (!$entity) {
            return $this->responseJson(__('messages.Record not found'), 404);
        }

        return $this->handleExceptions(function () use ($entity, $data) {
            $saved = $this->getRepository()->save($entity, $data);
            return $this->responseJson(__('messages.saved'), 200, $saved);
        });
    }
}
