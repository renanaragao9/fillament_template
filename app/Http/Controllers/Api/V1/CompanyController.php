<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\V1\Company\StoreCompanyRequest;
use App\Http\Requests\Api\V1\Company\UpdateCompanyRequest;
use App\Http\Resources\Api\V1\Company\CompanyResource;
use App\Models\Company;
use App\Services\Company\DestroyCompanyService;
use App\Services\Company\IndexCompanyService;
use App\Services\Company\ShowCompanyService;
use App\Services\Company\StoreCompanyService;
use App\Services\Company\UpdateCompanyService;
use Illuminate\Http\JsonResponse;

class CompanyController extends BaseController
{
    public function index(IndexCompanyService $indexCompanyService): JsonResponse
    {
        $this->authorize('viewAny', Company::class);

        return $this->successResponse(
            data: CompanyResource::collection($indexCompanyService->run()),
            message: 'Empresas listadas com sucesso.'
        );
    }

    public function show(Company $company, ShowCompanyService $showCompanyService): JsonResponse
    {
        $this->authorize('view', $company);

        return $this->successResponse(
            data: new CompanyResource($showCompanyService->run($company)),
            message: 'Empresa encontrada.'
        );
    }

    public function store(
        StoreCompanyRequest $storeCompanyRequest,
        StoreCompanyService $storeCompanyService
    ): JsonResponse {
        $this->authorize('create', Company::class);

        $data = $storeCompanyRequest->validated();
        $company = $storeCompanyService->run($data);

        return $this->successResponse(
            data: new CompanyResource($company),
            message: 'Empresa criada com sucesso.'
        );
    }

    public function update(
        UpdateCompanyRequest $updateCompanyRequest,
        Company $company,
        UpdateCompanyService $updateCompanyService
    ): JsonResponse {
        $this->authorize('update', $company);

        $data = $updateCompanyRequest->validated();
        $company = $updateCompanyService->run($company, $data);

        return $this->successResponse(
            data: new CompanyResource($company),
            message: 'Empresa atualizada com sucesso.'
        );
    }

    public function destroy(Company $company, DestroyCompanyService $destroyCompanyService): JsonResponse
    {
        $this->authorize('delete', $company);

        $destroyCompanyService->run($company);

        return $this->successResponse(
            data: null,
            message: 'Empresa removida com sucesso.'
        );
    }
}
