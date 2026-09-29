<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PolicyController extends Controller
{
    public function show(Request $request): View
    {
        $returnTo = in_array($request->query('return_to'), ['login', 'register', 'dashboard'], true)
            ? $request->query('return_to')
            : 'login';

        $request->session()->put([
            'policy_opened_at' => now()->toIso8601String(),
            'policy_revision' => config('policy.revision'),
            'policy_return_to' => $returnTo,
        ]);

        return view('auth.policy', [
            'policyRevision' => config('policy.revision'),
            'returnTo' => $returnTo,
        ]);
    }

    public function accept(Request $request): RedirectResponse
    {
        $request->validate([
            'policy_acknowledged' => ['accepted'],
        ]);

        abort_unless(
            $request->session()->get('policy_revision') === config('policy.revision')
                && $request->session()->has('policy_opened_at'),
            419,
            'Please open the current policy before accepting it.'
        );

        $returnTo = $request->session()->pull('policy_return_to', 'dashboard');

        if ($request->user()) {
            $request->user()->acceptCurrentPolicy($request->session()->get('policy_opened_at'));
            $request->session()->forget(['policy_opened_at', 'policy_revision']);
        } elseif ($returnTo === 'register' && $request->session()->has('policy_pending_registration')) {
            $pending = $request->session()->pull('policy_pending_registration');
            $user = User::create([
                ...$pending,
                'role' => 'worker',
                'is_active' => false,
                'policy_revision' => config('policy.revision'),
                'policy_viewed_at' => $request->session()->get('policy_opened_at'),
                'policy_accepted_at' => now(),
            ]);

            event(new Registered($user));
            $request->session()->forget(['policy_opened_at', 'policy_revision', 'policy_acknowledged']);

            return redirect()->route('login')->with('success', 'Your registration has been submitted successfully. A manager will review and activate your account. You will be able to log in once your account is activated.');
        } else {
            $request->session()->put('policy_acknowledged', true);
        }

        return match ($returnTo) {
            'login' => redirect()->route('login'),
            'register' => redirect()->route('register'),
            default => redirect()->intended($request->user()->isManager()
                ? route('manager.dashboard', absolute: false)
                : route('worker.dashboard', absolute: false)),
        };
    }
}