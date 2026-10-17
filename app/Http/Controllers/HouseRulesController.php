<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class HouseRulesController extends Controller
{
    public function show(Request $request): View
    {
        $request->session()->put('house_rules_opened_at', now()->toIso8601String());

        return view('auth.house-rules', [
            'houseRulesRevision' => config('house_rules.revision'),
            'hasAcknowledgedHouseRules' => $request->user()->hasAcknowledgedCurrentHouseRules(),
        ]);
    }

    public function acknowledge(Request $request): RedirectResponse
    {
        $request->validate([
            'house_rules_acknowledged' => ['accepted'],
        ]);

        abort_unless($request->session()->has('house_rules_opened_at'), 419, 'Please open the house rules before acknowledging them.');

        $request->user()->acknowledgeCurrentHouseRules(
            Carbon::parse($request->session()->pull('house_rules_opened_at'))
        );

        return redirect()->intended($request->user()->isManager()
            ? route('manager.dashboard', absolute: false)
            : route('worker.dashboard', absolute: false));
    }
}