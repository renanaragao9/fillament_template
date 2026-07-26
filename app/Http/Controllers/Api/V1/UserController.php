<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\V1\User\StoreUserRequest;
use App\Http\Requests\Api\V1\User\UpdateUserRequest;
use App\Http\Resources\Api\V1\User\UserResource;
use App\Models\User;
use App\Services\User\DestroyUserService;
use App\Services\User\IndexUserService;
use App\Services\User\StoreUserService;
use App\Services\User\UpdateUserService;
use Illuminate\Http\JsonResponse;

class UserController extends BaseController
{
    public function index(IndexUserService $indexUserService): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        return $this->successResponse(
            data: UserResource::collection($indexUserService->run()),
            message: 'Usuários listados com sucesso.'
        );
    }

    public function show(User $user): JsonResponse
    {
        $this->authorize('view', $user);

        return $this->successResponse(
            data: new UserResource($user->load('role')),
            message: 'Usuário encontrado.'
        );
    }

    public function store(
        StoreUserRequest $storeUserRequest,
        StoreUserService $storeUserService
    ): JsonResponse {
        $this->authorize('create', User::class);

        $data = $storeUserRequest->validated();
        $user = $storeUserService->run($data);

        if (! $user) {
            return $this->errorResponse(
                errors: ['role_id' => 'Perfil inválido.'],
                message: 'Perfil inválido.',
                statusCode: 422
            );
        }

        return $this->successResponse(
            data: new UserResource($user),
            message: 'Usuário criado com sucesso.'
        );
    }

    public function update(
        UpdateUserRequest $updateUserRequest,
        User $user,
        UpdateUserService $updateUserService
    ): JsonResponse {
        $this->authorize('update', $user);

        $data = $updateUserRequest->validated();
        $user = $updateUserService->run($user, $data);

        if (! $user) {
            return $this->errorResponse(
                errors: ['role_id' => 'Perfil inválido.'],
                message: 'Perfil inválido.',
                statusCode: 422
            );
        }

        return $this->successResponse(
            data: new UserResource($user),
            message: 'Usuário atualizado com sucesso.'
        );
    }

    public function destroy(User $user, DestroyUserService $destroyUserService): JsonResponse
    {
        $this->authorize('delete', $user);

        $destroyUserService->run($user);

        return $this->successResponse(
            data: null,
            message: 'Usuário removido com sucesso.'
        );
    }
}
