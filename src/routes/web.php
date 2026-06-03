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

Route::get('/widget', [TicketController::class, 'widget'])->name('tickets.widget');
Route::prefix('api')->group(function () {
    Route::post('/tickets', [TicketController::class, 'store'])->name('api.tickets.store');
});

Route::middleware(['auth', "role:$adminRole"])->prefix('admin')->group(function () {
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::get('/tickets-page', [TicketController::class, 'adminTicketsPage'])->name('admin.tickets.page');

    Route::prefix('api')->group(function () {
        Route::get('/tickets/statistics', [TicketController::class, 'statistics'])->name('admin.api.tickets.statistics');
        Route::get('/tickets', [TicketController::class, 'index'])->name('admin.api.tickets.index');
        Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('admin.api.tickets.show');
        Route::patch('/tickets/{ticket}', [TicketController::class, 'update'])->name('admin.api.tickets.update');
    });
});
