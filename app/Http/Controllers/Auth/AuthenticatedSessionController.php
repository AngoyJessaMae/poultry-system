<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
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
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Check if credentials are valid first
        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        $user = Auth::user();
        
        // Check if user is active before allowing login
        if (!$user->is_active) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Your account is still pending manager approval. Please wait for an administrator to activate your account before logging in.',
            ]);
        }

        $request->session()->regenerate();

        if (!$user->hasAcceptedCurrentPolicy()) {
            return redirect()->route('account-policy', ['return_to' => 'dashboard']);
        }

        if ($user->isManager()) {
            return redirect()->intended(route('manager.dashboard', absolute: false));
        }

        if ($user->isWorker()) {
            return redirect()->intended(route('worker.dashboard', absolute: false));
        }

        return redirect()->intended(route('dashboard', absolute: false));
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
}