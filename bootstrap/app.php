<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => route('filament.admin.auth.login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                $previous = $e->getPrevious();

                if ($previous instanceof ModelNotFoundException) {
                    $model = class_basename($previous->getModel());

                    return response()->json([
                        'status' => 'error',
                        'message' => "{$model} não encontrado.",
                        'errors' => [$model => "{$model} não encontrado."],
                    ], 404);
                }

                $message = $e->getMessage();

                if (blank($message) || $message === 'Not Found') {
                    $message = 'Recurso não encontrado.';
                }

                return response()->json([
                    'status' => 'error',
                    'message' => $message,
                    'errors' => ['resource' => $message],
                ], 404);
            }
        });

        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                $message = $e->getMessage();

                if (blank($message) || $message === 'This action is unauthorized.') {
                    $message = 'Você não tem permissão para executar esta ação.';
                }

                return response()->json([
                    'status' => 'error',
                    'message' => $message,
                    'errors' => ['authorization' => $message],
                ], 403);
            }
        });
    })->create();
