<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'contact_number' => ['required', 'digits:11'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($request->session()->get('policy_revision') !== config('policy.revision')
            || !$request->session()->has('policy_opened_at')) {
            $request->session()->put('policy_pending_registration', [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'contact_number' => $validated['contact_number'],
                'password' => Hash::make($validated['password']),
            ]);

            return redirect()->route('account-policy', ['return_to' => 'register'])
                ->withErrors(['policy_acknowledged' => 'Please read and accept the current policy to continue.']);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'contact_number' => $validated['contact_number'],
            'password' => $validated['password'],
            'role' => 'worker',
            'is_active' => false, // Self-registered workers need manager approval
            'policy_revision' => config('policy.revision'),
            'policy_viewed_at' => $request->session()->get('policy_opened_at'),
            'policy_accepted_at' => now(),
        ]);

        $request->session()->forget(['policy_opened_at', 'policy_revision', 'policy_return_to', 'policy_acknowledged', 'policy_pending_registration']);

        event(new Registered($user));

        // Don't auto-login inactive users - they need approval first
        return redirect()->route('login')->with('success', 'Your registration has been submitted successfully. A manager will review and activate your account. You will be able to log in once your account is activated.');
    }

}