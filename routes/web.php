<?php

use App\Http\Controllers\AvatarController;
use App\Http\Controllers\ExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin/login');
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/admin/export', ExportController::class)->name('admin.export');

    Route::get('/storage/avatars/{user}', AvatarController::class)->name('avatars.serve');
});
