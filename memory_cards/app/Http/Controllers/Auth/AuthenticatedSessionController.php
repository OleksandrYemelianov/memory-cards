<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\UserDataService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function __construct(private UserDataService $userData)
    {
    }

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect('/');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function demo(Request $request): RedirectResponse
    {
        return $this->authUser([
            'credentials' => [
                'email'    => 'demo@demo.com',
                'password' => 'demodemo',
            ],
            'id' => 1,
        ], $request);
    }

    public function demoUa(Request $request): RedirectResponse
    {
        return $this->authUser([
            'credentials' => [
                'email'    => 'demoua@demoua.com',
                'password' => 'demouademoua',
            ],
            'id' => 2,
        ], $request);
    }

    private function authUser(array $data, Request $request): RedirectResponse
    {
        if (Auth::attempt($data['credentials'])) {
            $this->userData->import($data['id']);
            $request->session()->regenerate();
        }

        return redirect('/');
    }
}
