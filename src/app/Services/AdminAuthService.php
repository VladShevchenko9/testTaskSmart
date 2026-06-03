<?php

namespace App\Services;

use App\Enums\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthService
{
    public function attemptAdminLogin(array $credentials, Request $request): bool
    {
        if (!Auth::attempt($credentials)) {
            return false;
        }

        if (!Auth::user()?->hasRole(Role::ADMIN->value)) {
            Auth::logout();

            return false;
        }

        $request->session()->regenerate();

        return true;
    }

    public function logout(Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
