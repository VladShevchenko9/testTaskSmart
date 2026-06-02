<?php

use App\Enums\Role;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

$adminRole = Role::ADMIN->value;

Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');

Route::middleware(['auth', "role:$adminRole"])->prefix('admin')->group(function () {

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

});
