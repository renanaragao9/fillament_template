<?php

use App\Http\Controllers\ExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin/login');
});

Route::get('/admin/export', ExportController::class)
    ->middleware(['web', 'auth'])
    ->name('admin.export');
