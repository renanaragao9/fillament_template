<?php

namespace App\Http\Controllers;

use App\Filament\Reports\Company\CompanyReport;
use App\Filament\Reports\Permission\PermissionReport;
use App\Filament\Reports\Role\RoleReport;
use App\Filament\Reports\User\UserReport;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class ExportController extends BaseController
{
    public function __invoke(Request $request)
    {
        $resourcePath = $request->query('resource');
        $search = $request->query('search');
        $user = $request->user();

        $report = match ($resourcePath) {
            'companies' => new CompanyReport,
            'users' => new UserReport,
            'roles' => new RoleReport,
            'permissions' => new PermissionReport,
            default => abort(404, 'Recurso não encontrado.'),
        };

        return $report->download($user, $search);
    }
}
