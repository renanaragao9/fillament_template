<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\V1\Auth\LoginAuthRequest;
use App\Http\Resources\Api\V1\Auth\UserAuthResource;
use App\Services\Auth\LoginAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    public function login(
        LoginAuthRequest $loginAuthRequest,
        LoginAuthService $loginAuthService
    ): JsonResponse {
        $data = $loginAuthRequest->validated();
        $user = $loginAuthService->run($data);

        if (! $user) {
            return $this->errorResponse(
                errors: ['credentials' => 'Email ou senha inválidos.'],
                message: 'Email ou senha inválidos.',
                statusCode: 401
            );
        }

        return $this->successResponse(
            data: new UserAuthResource($user),
            message: 'Login realizado com sucesso.'
        );
    }

    public function me(Request $request): JsonResponse
    {
        return $this->successResponse(
            data: new UserAuthResource($request->user()),
            message: 'Usuário autenticado.'
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(
            data: null,
            message: 'Logout realizado com sucesso.'
        );
    }
}
