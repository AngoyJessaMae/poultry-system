<div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700" data-account-policy>
    <details id="account-policy-details" class="group">
        <summary class="flex cursor-pointer list-none items-center justify-between font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
            <span><a href="{{ route('account-policy', ['return_to' => request()->is('register') ? 'register' : 'login']) }}" class="underline hover:text-orange-600">{{ __('Read Account and Work Process Policy') }}</a></span>
            <span class="ml-4 text-lg text-slate-500 transition group-open:rotate-180" aria-hidden="true">&#8964;</span>
        </summary>

        <div class="mt-3 space-y-2 border-t border-slate-200 pt-3 leading-5">
            <p>{{ __('Managers may review the overall farm work process through authorized summaries and reports.') }}</p>
            <p>{{ __('Workers are responsible for accurately recording operational activities, including feeding, growth, health, mortality, and sales records.') }}</p>
            <p>{{ __('Users may access only the functions permitted for their role. User credentials are private: managers may manage accounts or initiate password resets, but must never view or request a user password.') }}</p>

        </div>
    </details>

    <label for="policy_acknowledged" class="mt-4 flex items-start">
        <input id="policy_acknowledged" type="checkbox" class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="policy_acknowledged" value="1" required>
        <span class="ml-2 text-sm text-gray-600">{{ __('I acknowledge and agree to this policy.') }}</span>
    </label>
    <x-input-error :messages="$errors->get('policy_acknowledged')" class="mt-2" />
</div>
