<?php

use App\Enums\Role;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

$adminRole = Role::ADMIN->value;

Route::get('/', [TicketController::class, 'welcome'])->name('welcome');
Route::get('/login', fn() => redirect()->route('admin.login'))->name('login');

Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');

Route::middleware(['auth', "role:$adminRole"])->prefix('admin')->group(function () {
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/tickets', [TicketController::class, 'index'])->name('admin.tickets.index');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('admin.tickets.show');
    Route::patch('/tickets/{ticket}', [TicketController::class, 'update'])->name('admin.tickets.update');
});
