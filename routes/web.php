<?php

use App\Http\Controllers\ExportController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return redirect('/admin/login');
});

Route::get('/admin/export', ExportController::class)
    ->middleware(['web', 'auth'])
    ->name('admin.export');

Route::get('/storage/avatars/{path}', function (string $path) {
    $possiblePaths = [
        'avatars/' . $path,
        'private/avatars/' . $path,
    ];

    foreach ($possiblePaths as $fullPath) {
        if (Storage::disk('local')->exists($fullPath)) {
            return response()->file(Storage::disk('local')->path($fullPath));
        }
    }

    abort(404);
})->where('path', '.*')->name('avatars.serve');
