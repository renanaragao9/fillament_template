<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\V1\Permission\StorePermissionRequest;
use App\Http\Requests\Api\V1\Permission\UpdatePermissionRequest;
use App\Http\Resources\Api\V1\Permission\PermissionResource;
use App\Models\Permission;
use App\Services\Permission\DestroyPermissionService;
use App\Services\Permission\IndexPermissionService;
use App\Services\Permission\ShowPermissionService;
use App\Services\Permission\StorePermissionService;
use App\Services\Permission\UpdatePermissionService;
use Illuminate\Http\JsonResponse;

class PermissionController extends BaseController
{
    public function index(IndexPermissionService $indexPermissionService): JsonResponse
    {
        $this->authorize('viewAny', Permission::class);

        return $this->successResponse(
            data: PermissionResource::collection($indexPermissionService->run()),
            message: 'Permissões listadas com sucesso.'
        );
    }

    public function show(
        Permission $permission,
        ShowPermissionService $showPermissionService
    ): JsonResponse {
        $this->authorize('view', $permission);

        return $this->successResponse(
            data: new PermissionResource($showPermissionService->run($permission)),
            message: 'Permissão encontrada.'
        );
    }

    public function store(
        StorePermissionRequest $storePermissionRequest,
        StorePermissionService $storePermissionService
    ): JsonResponse {
        $this->authorize('create', Permission::class);

        $data = $storePermissionRequest->validated();
        $permission = $storePermissionService->run($data);

        return $this->successResponse(
            data: new PermissionResource($permission),
            message: 'Permissão criada com sucesso.'
        );
    }

    public function update(
        UpdatePermissionRequest $updatePermissionRequest,
        Permission $permission,
        UpdatePermissionService $updatePermissionService
    ): JsonResponse {
        $this->authorize('update', $permission);

        $data = $updatePermissionRequest->validated();
        $permission = $updatePermissionService->run($permission, $data);

        return $this->successResponse(
            data: new PermissionResource($permission),
            message: 'Permissão atualizada com sucesso.'
        );
    }

    public function destroy(
        Permission $permission,
        DestroyPermissionService $destroyPermissionService
    ): JsonResponse {
        $this->authorize('delete', $permission);

        $destroyPermissionService->run($permission);

        return $this->successResponse(
            data: null,
            message: 'Permissão removida com sucesso.'
        );
    }
}
