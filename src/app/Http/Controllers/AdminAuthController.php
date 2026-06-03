<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminLoginRequest;
use App\Services\AdminAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    public function __construct(private readonly AdminAuthService $adminAuthService)
    {
    }

    public function showLogin(): View
    {
        return view('admin.auth.login');
    }

    public function login(AdminLoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if ($this->adminAuthService->attemptAdminLogin($credentials, $request)) {
            return redirect()->route('admin.tickets.page');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'Invalid credentials',
            ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->adminAuthService->logout($request);

        return redirect()->route('admin.login');
    }
}
