<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->tenantIsActive()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Empresa inativa ou período de teste expirado.',
                'errors' => ['company' => 'Empresa inativa ou período de teste expirado.'],
            ], 403);
        }

        return $next($request);
    }
}
