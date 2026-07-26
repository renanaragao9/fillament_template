<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\V1\Role\StoreRoleRequest;
use App\Http\Requests\Api\V1\Role\UpdateRoleRequest;
use App\Http\Resources\Api\V1\Role\RoleResource;
use App\Models\Role;
use App\Services\Role\DestroyRoleService;
use App\Services\Role\IndexRoleService;
use App\Services\Role\ShowRoleService;
use App\Services\Role\StoreRoleService;
use App\Services\Role\UpdateRoleService;
use Illuminate\Http\JsonResponse;

class RoleController extends BaseController
{
    public function index(IndexRoleService $indexRoleService): JsonResponse
    {
        $this->authorize('viewAny', Role::class);

        return $this->successResponse(
            data: RoleResource::collection($indexRoleService->run()),
            message: 'Perfis listados com sucesso.'
        );
    }

    public function show(Role $role, ShowRoleService $showRoleService): JsonResponse
    {
        $this->authorize('view', $role);

        return $this->successResponse(
            data: new RoleResource($showRoleService->run($role)),
            message: 'Perfil encontrado.'
        );
    }

    public function store(
        StoreRoleRequest $storeRoleRequest,
        StoreRoleService $storeRoleService
    ): JsonResponse {
        $this->authorize('create', Role::class);

        $data = $storeRoleRequest->validated();
        $role = $storeRoleService->run($data);

        return $this->successResponse(
            data: new RoleResource($role),
            message: 'Perfil criado com sucesso.'
        );
    }

    public function update(
        UpdateRoleRequest $updateRoleRequest,
        Role $role,
        UpdateRoleService $updateRoleService
    ): JsonResponse {
        $this->authorize('update', $role);

        $data = $updateRoleRequest->validated();
        $role = $updateRoleService->run($role, $data);

        return $this->successResponse(
            data: new RoleResource($role),
            message: 'Perfil atualizado com sucesso.'
        );
    }

    public function destroy(Role $role, DestroyRoleService $destroyRoleService): JsonResponse
    {
        $this->authorize('delete', $role);

        $destroyRoleService->run($role);

        return $this->successResponse(
            data: null,
            message: 'Perfil removido com sucesso.'
        );
    }
}
